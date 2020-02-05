<?php


namespace App\Helpers;


use App\Models\ProfileStatusType;
use App\Models\Role;
use App\Models\RoleAction;
use App\Models\RoleCategorie;
use App\Models\RoleDefinition;
use App\User;

class RoleAssignHelper
{
    private static $viewAll;
    private static $viewOwn;
    private static $create;
    private static $removeAll;
    private static $removeOwn;
    private static $editAll;
    private static $editOwn;

    private static $pendingId;
    private static $rejectedId;
    private static $approvedId;

    private static $anyId = null;

    private static function init()
    {
        /// Actions
        self::$viewAll = RoleAction::where('name', 'view all')->first()->id;
        self::$viewOwn = RoleAction::where('name', 'view own')->first()->id;
        self::$create = RoleAction::where('name', 'create')->first()->id;
        self::$removeAll = RoleAction::where('name', 'remove all')->first()->id;
        self::$removeOwn = RoleAction::where('name', 'remove own')->first()->id;
        self::$editAll = RoleAction::where('name', 'edit all')->first()->id;
        self::$editOwn = RoleAction::where('name', 'edit own')->first()->id;

        // Statuses
        self::$pendingId = ProfileStatusType::where('name', 'Pending')->first()->id;
        self::$rejectedId = ProfileStatusType::where('name', 'Rejected')->first()->id;
        self::$approvedId = ProfileStatusType::where('name', 'Approved')->first()->id;


    }

    public static function assignAsSuperAdmin(User $user)
    {
        self::init();

        $userId = $user->id;

        $role = Role::where('name', 'Super Admin')->first();

        try {
            $user->roles()->create([
                'user_id' => $userId,
                'role_id' => $role->id
            ]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        //VA, EA, DA, CA
        RoleCategoryHelper::setScenariosPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setConditionsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setNonResponseConditionsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setTranslationsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setTranslationsContentPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setDictionariesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setSourcesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setCountriesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setCitiesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setRegionsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setProfileDetailTypesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VAS, EAS, DAS, CAS
        RoleCategoryHelper::setBotProfilesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VAS, EAS, DAS, CAS
        RoleCategoryHelper::setBotProfileDetailsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setLanguagesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setProfileDetailValueTypesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setRolesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setUsersPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setAssigningRolesToUserPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EA, DA, CA
        RoleCategoryHelper::setSourcesBotProfilesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);
        //Allowed
//        RoleCategoryHelper::setTestScenariosPermissions();
    }

    public static function assignAsEditor(User $user, $countryId)
    {
        self::init();

        $userId = $user->id;

        $role = Role::where('name', 'Editor')->first();

        try {
            $user->roles()->create([
                'user_id' => $userId,
                'role_id' => $role->id
            ]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        //VA, EF, DF, CF    Dictionaries
        RoleCategoryHelper::setDictionariesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    Sources
        RoleCategoryHelper::setSourcesPermissions($role, self::$viewAll);

        //VC, EF, DF, CF    Countries
        RoleCategoryHelper::setCountriesPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VC, EF, DF, CF    Cities
        RoleCategoryHelper::setCitiesPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VC, EF, DF, CF    Regions
        RoleCategoryHelper::setRegionsPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VA, EF, DF, CF    ProfileDetailTypes
        RoleCategoryHelper::setProfileDetailTypesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    ProfileDetailValueTypes
        RoleCategoryHelper::setProfileDetailValueTypesPermissions($role, self::$viewAll);

        //VCP, ECP, DFP, CFP    BotProfiles
        RoleCategoryHelper::setBotProfilesPermissions($role, self::$viewAll, self::$editAll, null, null, $countryId, self::$pendingId);

        //VCP, ECP, DFP, CFP    BotProfileDetails
        RoleCategoryHelper::setBotProfileDetailsPermissions($role, self::$viewAll, self::$editAll, null, null, $countryId, self::$pendingId);

        //VA, EF, DF, CF    Languages
        RoleCategoryHelper::setLanguagesPermissions($role, self::$viewAll);

        //VC, EF, DF, CF    Users
        RoleCategoryHelper::setUsersPermissions($role, self::$viewAll);
    }

    public static function assignAsWriter(User $user, $countryId)
    {
        self::init();

        $userId = $user->id;

        $role = Role::where('name', 'Writer')->first();

        try {
            $user->roles()->create([
                'user_id' => $userId,
                'role_id' => $role->id
            ]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        //VA, EF, DF, CF    Dictionaries
        RoleCategoryHelper::setDictionariesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    Sources
        RoleCategoryHelper::setSourcesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    Languages
        RoleCategoryHelper::setLanguagesPermissions($role, self::$viewAll);

        //VC, EF, DF, CF    Countries
        RoleCategoryHelper::setCountriesPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VC, EF, DF, CF    Cities
        RoleCategoryHelper::setCitiesPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VC, EF, DF, CF    Regions
        RoleCategoryHelper::setRegionsPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VO, EF, DF, CF    Users
        RoleCategoryHelper::setUsersPermissions($role, self::$viewOwn);

        //VA, EF, DF, CF    ProfileDetailTypes
        RoleCategoryHelper::setProfileDetailTypesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    ProfileDetailValueTypes
        RoleCategoryHelper::setProfileDetailValueTypesPermissions($role, self::$viewAll);

        //VOP, EXP, DOP, CCP    BotProfiles
        RoleCategoryHelper::setBotProfilesPermissions($role, self::$viewOwn, self::$editOwn, self::$removeOwn, self::$create, null, self::$pendingId);

        //VOP, EOP, DOP, COP    BotProfileDetails
        RoleCategoryHelper::setBotProfileDetailsPermissions($role, self::$viewOwn, self::$editOwn, self::$removeOwn, self::$create, null, self::$pendingId);

    }

    public static function assignAsScenarist(User $user, $countryId)
    {
        self::init();

        $userId = $user->id;

        $role = Role::where('name', 'Scenarist')->first();

        try {
            $user->roles()->create([
                'user_id' => $userId,
                'role_id' => $role->id
            ]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        //VO, EO, DO, CO    Scenarios
        RoleCategoryHelper::setScenariosPermissions($role, self::$viewOwn, self::$editOwn, self::$removeOwn, self::$create);

        //VA, EA, DA, CA    Rules
        RoleCategoryHelper::setRulesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VO, EO, DO, CO    Conditions
        RoleCategoryHelper::setConditionsPermissions($role, self::$viewOwn, self::$editOwn, self::$removeOwn, self::$create);

        //VO, EO, DO, CO    NonResponseConditions
        RoleCategoryHelper::setNonResponseConditionsPermissions($role, self::$viewOwn, self::$editOwn, self::$removeOwn, self::$create);

        //VA, EA, DA, CA    Translations
        RoleCategoryHelper::setTranslationsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create);

        //VA, EF, DF, CF    Dictionaries
        RoleCategoryHelper::setDictionariesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    Sources
        RoleCategoryHelper::setSourcesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    Languages
        RoleCategoryHelper::setLanguagesPermissions($role, self::$viewAll);

        //VC, EF, DF, CF    Countries
        RoleCategoryHelper::setCountriesPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VC, EF, DF, CF    Cities
        RoleCategoryHelper::setCitiesPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VC, EF, DF, CF    Regions
        RoleCategoryHelper::setRegionsPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VO, EF, DF, CF    Users
        RoleCategoryHelper::setUsersPermissions($role, self::$viewOwn);

        //VA, EF, DF, CF    ProfileDetailTypes
        RoleCategoryHelper::setProfileDetailTypesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    BotProfiles
        RoleCategoryHelper::setBotProfilesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    BotProfileDetails
        RoleCategoryHelper::setBotProfileDetailsPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    ProfileDetailValueTypes
        RoleCategoryHelper::setProfileDetailValueTypesPermissions($role, self::$viewAll);

        //Allowed
//        RoleCategoryHelper::setTestScenariosPermissions();
    }

    public static function assignAsCountryAdmin(User $user, $countryId)
    {
        self::init();

        $userId = $user->id;

        $role = Role::where('name', 'Sweden Admin')->first();

        try {
            $user->roles()->create([
                'user_id' => $userId,
                'role_id' => $role->id
            ]);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        //VA, EF, DF, CF    Translations
        RoleCategoryHelper::setTranslationsPermissions($role, self::$viewAll);

        //VC, EC, DC, CC    TranslationsContent
        RoleCategoryHelper::setTranslationsContentPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create, $countryId);

        //VA, EF, DF, CF    Dictionaries
        RoleCategoryHelper::setDictionariesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    Sources
        RoleCategoryHelper::setSourcesPermissions($role, self::$viewAll);

        //VC, EF, DF, CF    Countries
        RoleCategoryHelper::setCountriesPermissions($role, self::$viewAll, null, null, null, $countryId);

        //VC, EC, DC, CC    Cities
        RoleCategoryHelper::setCitiesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create, $countryId);

        //VC, EC, DC, CC    Regions
        RoleCategoryHelper::setRegionsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create, $countryId);

        //VA, EF, DF, CF    ProfileDetailTypes
        RoleCategoryHelper::setProfileDetailTypesPermissions($role, self::$viewAll);

        //VA, EF, DF, CF    ProfileDetailValueTypes
        RoleCategoryHelper::setProfileDetailValueTypesPermissions($role, self::$viewAll);

        //VCS, ECS, DCS, CCS    BotProfiles
        RoleCategoryHelper::setBotProfilesPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create, $countryId);

        //VCS, ECS, DCS, CCS    BotProfileDetails
        RoleCategoryHelper::setBotProfileDetailsPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create, $countryId);

        //VA, EF, DF, CF    Languages
        RoleCategoryHelper::setLanguagesPermissions($role, self::$viewAll);

        //VA, EF, DF, CA    Users
        RoleCategoryHelper::setUsersPermissions($role, self::$viewAll, null, null, self::$create);

        //AC, EC, DC, CC     AssigningRolesToUser
        RoleCategoryHelper::setAssigningRolesToUserPermissions($role, self::$viewAll, self::$editAll, self::$removeAll, self::$create, $countryId);
    }

}