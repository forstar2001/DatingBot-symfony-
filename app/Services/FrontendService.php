<?php

namespace App\Services;

use App\Models\DialogParticipant;
use App\Models\Language;
use App\Models\MessageTemplateContent;
use App\Models\ProfileDetailType;
use MaxMind\Db\Reader;
use App\Models\BotProfile;
use App\Models\PersonProfile;
use App\Models\PersonProfileDetail;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;
use App\Models\Dialog;
use App\Services\DialogService;
use App\Models\SourcesBotProfiles;
use Mockery\Exception;


class FrontendService
{
    private static function pickProfiles(?Country $country, ?Region $region, ?City $city, $sourceId) {


        $profileCityId = config('database.profile_detail_city_id');
        $sourceProfileStatusActiveId = config('database.source_profile_status_active_id');

        //find active profiles with specific source
        $botProfiles = BotProfile::with(['sources', 'region', 'bot_profile_details' => function($q) {
            $q->whereIn('profile_detail_type_id',
                [
                    config('database.profile_detail_city_id'),
                    config('database.profile_detail_age_id'),
                    config('database.profile_detail_photo_id'),
                    config('database.profile_detail_realname_id')
                ]);
        }])
            ->whereHas('sources', function ($query) use ($sourceProfileStatusActiveId, $sourceId)  {
                $query->where('source_id', $sourceId)->where('status_id', $sourceProfileStatusActiveId);
            })->get();

        foreach ($botProfiles as $profile) {
            $profile->link = SourcesBotProfiles::where('bot_profile_id', $profile->id)
                            ->where('source_id', $sourceId)->first()->link;
        }

        $cnt = $botProfiles->count();
        if ($cnt == 0) {
            //no profiles at all
            return $botProfiles->toArray();
        }

        if ($country != null) {
            $botProfilesCountry = $botProfiles->filter(function ($item) use ($country) {
                return ($item->country->name == $country->name);
            });
        } else {
            $botProfilesCountry = collect();
        }

        $cnt = $botProfilesCountry->count();
        if ($cnt == 0) {
            //no profiles, get default country profiles

            $defaultCountry = Country::find(config('database.frontend_default_country_id'));
            $botProfilesDefaultCountry = $botProfiles->filter(function($item) use ($defaultCountry) {
                return ($item->country->name == $defaultCountry->name);
            });

            return $botProfilesDefaultCountry->toArray();
        }

        $botProfilesRegion = collect();

        if ($region != NULL) {
            $botProfilesRegion = $botProfilesCountry->filter(function($item) use ($region) {
                return ($item->region->name == $region->name);
            });

            if ($botProfilesRegion->count() == 0) {
                //return by country
                return $botProfilesCountry->toArray();
            }

        }

        if ($city != NULL) {
            $botProfilesCity = $botProfilesCountry->filter(function($item) use ($city, $profileCityId) {
                $profileCity = $item->bot_profile_details->where('profile_detail_type_id', $profileCityId)->first();
                return ($profileCity->name == $city->name);
            });

            if ($botProfilesCity->count() == 0) {
                return ($botProfilesRegion->count() > 0 ? $botProfilesRegion->toArray() : $botProfilesCountry->toArray());
            }

        } else {
            return ($botProfilesRegion->count() > 0 ? $botProfilesRegion->toArray() : $botProfilesCountry->toArray());
        }
    }



    private static function detectGeo($ip) {
        $dbFile = app_path('GEO/GeoLite2-City.mmdb');
        $reader = new Reader($dbFile);
        $ipInfo = $reader->get($ip);

        $result = [];
        $result['success'] = true;

        $countryCode = $ipInfo['country']['iso_code'];
        $result['country'] = Country::whereCode($countryCode)->first();
        if (empty($result['country'])) {
            $result['success'] = false;
            return $result;
        }

        if (isset($ipInfo['city']) && isset($ipInfo['city']['names']) && isset($ipInfo['city']['names']['en'])) {
            $city = $ipInfo['city']['names']['en'];
            $result['city'] = City::whereCode($city)->first();
        } else {
            $result['city'] = null;
        }

        if (isset($ipInfo['subdivisions'])
                && isset($ipInfo['subdivisions'][0])
                && isset($ipInfo['subdivisions'][0]['names'])
                && isset($ipInfo['subdivisions'][0]['names']['en'])) {

            $region = $ipInfo['subdivisions'][0]['names']['en'];
            $result['region'] = Region::whereCode($region)->first();
        }
        else {
            $result['region'] = null;
        }

        if (isset($ipInfo['location']) && isset($ipInfo['location']['time_zone'])) {
            $result['timezone'] = $ipInfo['location']['time_zone'];
        } else {
            $result['timezone'] = null;
        }

        return $result;
    }

    private static function getDateTimeFromTZ($timezone) {
        return new \DateTime("now", new \DateTimeZone($timezone) );
    }

    private static function getProfiles($country, $region, $city, $sourceId) {

        $profiles = self::pickProfiles($country, $region, $city, $sourceId);

        shuffle($profiles);

        return array_slice($profiles, 0, config('database.frontend_profiles_result_amount'));
    }


    private static function setPersonDetail($personId, $value, $typeId) {
        if ($value) {
            $personDetail = new PersonProfileDetail();
            $personDetail->person_profile_id = $personId;
            $personDetail->value = $value;
            $personDetail->profile_detail_type_id = $typeId;
            DialogService::setPersonDetails($personDetail);
        }
    }

    public static function setupDialog($profileId, $personId, $sourceId) {

        $dialog = new Dialog();
        $dialog->scenario_id = config('database.default_scenario_id');
        $dialog->source_id = $sourceId;

        $person = PersonProfile::with(['country', 'country.language'])->findOrFail($personId);
        $personLanguage = $person->country->language;

        $dialog->language_id = $personLanguage->id;
        $dialog->current_step = 0;
        $dialog->current_step_created_date = new \DateTime();
        DialogService::createDialog($dialog);

        $dialogParticipant = new DialogParticipant();
        $dialogParticipant->dialog_id = $dialog->id;
        $dialogParticipant->person_profile_id = $personId;
        $dialogParticipant->bot_profile_id = $profileId;
        DialogService::createDialogParticipants($dialogParticipant);

        return $dialog;


    }

    public static function getInitialDataForProfiles($ip, $sourceId) {

        $geo = self::detectGeo($ip);
        if ($geo['success'] == false || !in_array($geo['country']->id, config('database.frontend_enabled_countries'))) {
            $result['drop_link'] = config('database.frontend_default_drop_link');
            return $result;
        }
        $profiles = self::getProfiles($geo['country'], $geo['region'], $geo['city'], $sourceId);
        if ($geo['timezone'] != null)
            $dateTime = self::getDateTimeFromTZ($geo['timezone']);

        $person = new PersonProfile();
        $person->country_id = $geo['country']['id'];
        $person->region_id = $geo['region']['id'];
        $person->source_id = $sourceId;
        $person->name = $ip . '-' . date_format(isset($dateTime) ? $dateTime : new \DateTime(), 'Y-m-d H:i:s');
        DialogService::setPerson($person);

        //set person profile details
        self::setPersonDetail($person->id, $geo['city']['name'], config('database.profile_detail_city_id'));
        if ($geo['timezone'] != null)
            self::setPersonDetail($person->id, date_format($dateTime, 'Y-m-d H:i:s'), config('database.profile_detail_datetime_id'));

        $result['profiles'] = $profiles;
        $result['person_id'] = $person->id;
        $result['configs']['profile_detail_city_id'] = config('database.profile_detail_city_id');
        $result['configs']['profile_detail_age_id'] = config('database.profile_detail_age_id');
        $result['configs']['profile_detail_photo_id'] = config('database.profile_detail_photo_id');
        $result['configs']['profile_detail_realname_id'] = config('database.profile_detail_realname_id');

        return $result;
    }

    public static function getInitialDataForChat($profileId, $personId, $sourceId) {
        $profile =  BotProfile::with(['sources', 'bot_profile_details' => function($q) {
            $q->whereIn('profile_detail_type_id',
                [
                    config('database.profile_detail_photo_id'),
                    config('database.profile_detail_realname_id')
                ]);
        }])->findOrFail($profileId);

        $personLanguage = self::getPersonLanguage($personId);

        $templateIsWriting = MessageTemplateContent
            ::where('message_template_id', config('database.template_typing_id'))
            ->where('language_id', $personLanguage->id)->firstOrFail()->text;
        $templateOnline = MessageTemplateContent
            ::where('message_template_id', config('database.template_online_id'))
            ->where('language_id', $personLanguage->id)->firstOrFail()->text;
        $templateMatchedWith = MessageTemplateContent
            ::where('message_template_id', config('database.template_matched_with_id'))
            ->where('language_id', $personLanguage->id)->firstOrFail()->text;
        $templateTypeMessage = MessageTemplateContent
            ::where('message_template_id', config('database.template_type_message_id'))
            ->where('language_id', $personLanguage->id)->firstOrFail()->text;
        $templateOffline = MessageTemplateContent
            ::where('message_template_id', config('database.template_offline_id'))
            ->where('language_id', $personLanguage->id)->firstOrFail()->text;
        $profileUrl = SourcesBotProfiles::where('source_id', $sourceId)
                        ->where('bot_profile_id', $profileId)->firstOrFail()->link;

        $data['profile'] = $profile;
        $data['profile_url'] = $profileUrl;
        $data['templates']['writing'] = $templateIsWriting;
        $data['templates']['online'] = $templateOnline;
        $data['templates']['offline'] = $templateOffline;
        $data['templates']['matched_with'] = $templateMatchedWith;
        $data['templates']['type_message'] = $templateTypeMessage;
        $data['configs']['profile_detail_photo_id'] = config('database.profile_detail_photo_id');
        $data['configs']['profile_detail_realname_id'] = config('database.profile_detail_realname_id');

        return $data;
    }

    public static function getPersonLanguage($personId)
    {
        $defaultLang = Language::where('name', 'English')->first();
        $langValueTypeId = ProfileDetailType::where('variable_name', 'Language')->first()->id;
        $person = PersonProfile::with('person_profile_details')
            ->whereHas('person_profile_details',function ($q) use($langValueTypeId){
                $q->where('profile_detail_type_id', $langValueTypeId);
            })->find($personId);
        if(!$person){
            if(!$defaultLang){
                throw new \LogicException('can\'t get person language');
            }
            return $defaultLang;
        }
        $languageName = $person->person_profile_details
            ->where('profile_detail_type_id', $langValueTypeId)
            ->first();
        $language = Language::where('name', $languageName->value)->first();
        if(!$language){
            throw new \LogicException('can\'t get person language');
        }
        return $language;
    }
}