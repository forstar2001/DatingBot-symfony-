<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login/token', ['middleware' => 'cors', 'uses' => 'OauthLoginController@token']);//->middleware('auth:api');
Route::post('/login/refreshtoken', ['middleware' => 'cors', 'uses' => 'OauthLoginController@token']);
Route::post('/login/user', ['middleware' => 'cors', 'uses' => 'OauthLoginController@isRegistered']);
Route::post('/login/check-code', ['middleware' => 'cors', 'uses' => 'OauthLoginController@checkCode']);

Route::get('/frontend/detect_geo', ['middleware' => 'cors', 'uses' => 'FrontendController@detectGeo'] );
Route::get('/frontend/get_initial_data_profiles', ['middleware' => 'cors', 'uses' => 'FrontendController@getInitialDataForProfiles']);
Route::get('/frontend/get_initial_data_chat', ['middleware' => 'cors', 'uses' => 'FrontendController@getInitialDataForChat']);
Route::put('/frontend/set_outgoing_message_sent', ['middleware' => 'cors', 'uses' => 'FrontendController@setOutgoingMessageAsSent']);
Route::post('/frontend/send_incoming_message', ['middleware' => 'cors', 'uses' => 'FrontendController@sendIncomingMessage']);




Route::post('/entity/dialog/create', ['middleware' => 'cors', 'uses' => 'DialogController@create']);
//Route::get('/entity/dialog/get', ['middleware' => 'cors', 'uses' => 'DialogController@getAll']);
Route::post('/entity/dialog/set_person', ['middleware' => 'cors', 'uses' => 'DialogController@setPerson']);
Route::post('/entity/dialog/set_bot', ['middleware' => 'cors', 'uses' => 'DialogController@setBot']);
Route::post('/entity/dialog/set_person_details', ['middleware' => 'cors', 'uses' => 'DialogController@setPersonDetails']);
Route::post('/entity/dialog/set_bot_details', ['middleware' => 'cors', 'uses' => 'DialogController@setBotDetails']);
Route::post('/entity/dialog/save_incoming_message', ['middleware' => 'cors', 'uses' => 'DialogController@saveIncomingMessage']);
Route::post('/entity/dialog/set_outgoing_message_as_sent', ['middleware' => 'cors', 'uses' => 'DialogController@setOutgoingMessageAsSent']);
Route::get('/entity/dialog/get_outgoing_message', ['middleware' => 'cors', 'uses' => 'DialogController@getOutgoingMessage']);
Route::post('/entity/dialog/calculate_step', ['middleware' => 'cors', 'uses' => 'DialogController@calculateStep']);
Route::get('/entity/dialog/pick_profile_by_country_city', ['middleware' => 'cors', 'uses' => 'DialogController@pickProfileByCountryCity']);
Route::get('/entity/dialog/get_bot_profile_details', ['middleware' => 'cors', 'uses' => 'DialogController@getBotProfileDetails']);

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/scenario'], function()
{
    //scenario
    Route::post('/', ['middleware' => 'cors',  'uses' => 'ScenarioController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'ScenarioController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'ScenarioController@delete']);
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'ScenarioController@getById'] );
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ScenarioController@get']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/scenariotype'], function()
{
    //scenario type
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ScenarioTypeController@get']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/rule'], function() {
    //rule
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'RuleController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'RuleController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'RuleController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'RuleController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'RuleController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/condition'], function() {
    //condition
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'ConditionController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ConditionController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'ConditionController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'ConditionController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'ConditionController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/conditiontype'], function()
{
    //condition type
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ConditionTypeController@get']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/messagetemplate'], function() {
    //message template
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'MessageTemplateController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/messagetemplatecontent'], function() {
    //message template content
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'MessageTemplateContentController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateContentController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateContentController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateContentController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'MessageTemplateContentController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/language'], function()
{
    //language
    Route::get('/', ['middleware' => 'cors', 'uses' => 'LanguageController@get']);
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'LanguageController@getById']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/dictionary'], function() {
    //dictionary
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'DictionaryController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'DictionaryController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'DictionaryController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'DictionaryController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'DictionaryController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/dictionaryvalue'], function() {
    //dictionary values
    Route::get('/{id}/{dictionaryId}', ['middleware' => 'cors', 'uses' => 'DictionaryValueController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'DictionaryValueController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'DictionaryValueController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'DictionaryValueController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'DictionaryValueController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/profiledetailvaluetype'], function()
{
    //profile detail value type
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ProfileDetailValueTypeController@get']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/profiledetailtype'], function() {
    //profile detail type
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'ProfileDetailTypeController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ProfileDetailTypeController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'ProfileDetailTypeController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'ProfileDetailTypeController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'ProfileDetailTypeController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/source'], function()
{
    //source
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'SourceController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'SourceController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'SourceController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'SourceController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'SourceController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/botprofile'], function() {
    //bot profile
    Route::get('/{id}', ['middleware' => ['cors'], 'uses' => 'BotProfileController@getById']);
    Route::get('/', ['middleware' => ['cors'], 'uses' => 'BotProfileController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'BotProfileController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'BotProfileController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'BotProfileController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/botprofiledetail'], function() {
    //bot profile detail
    Route::get('/{id}', ['middleware' => ['cors'], 'uses' => 'BotProfileDetailController@getById']);
    Route::get('/', ['middleware' => ['cors'], 'uses' => 'BotProfileDetailController@get']);
    Route::post('/', ['middleware' => ['cors' ], 'uses' => 'BotProfileDetailController@create']);
    Route::post('/updatefile', ['middleware' => ['cors'], 'uses' => 'BotProfileDetailController@update']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'BotProfileDetailController@update']);
    Route::delete('/', ['middleware' => ['cors'],  'uses' => 'BotProfileDetailController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/country'], function() {
    //country
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'CountryController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'CountryController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'CountryController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'CountryController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'CountryController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/city'], function() {
    //city
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'CityController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'CityController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'CityController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'CityController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'CityController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/region'], function() {
    //region
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'RegionController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'RegionController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'RegionController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'RegionController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'RegionController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/nonresponsecondition'], function() {
    //non-response condition
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'NonresponseConditionController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'NonresponseConditionController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'NonresponseConditionController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'NonresponseConditionController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'NonresponseConditionController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/user'], function()
{
    //users
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'UserController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'UserController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'UserController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'UserController@update']);
    Route::delete('/', ['middleware' => 'cors','uses' => 'UserController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/role'], function() {
    //roles
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'RoleController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'RoleController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'RoleController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'RoleController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'RoleController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/role-definition'], function() {
    //role definitions
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'RoleDefinitionController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'RoleDefinitionController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'RoleDefinitionController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'RoleDefinitionController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'RoleDefinitionController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/user-role'], function() {
    //user roles
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'UserRoleController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'UserRoleController@get']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'UserRoleController@create']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'UserRoleController@update']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'UserRoleController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/role-category'], function()
{
    //role category
    Route::get('/', ['middleware' => 'cors', 'uses' => 'RoleCategoryController@get']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/role-action'], function()
{
    //role action
    Route::get('/', ['middleware' => 'cors', 'uses' => 'RoleActionController@get']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/profile-status-type'], function()
{
    //profile status type
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ProfileStatusTypeController@get']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/two-factor-auth'], function()
{
    //two-factor-auth
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'TwoFactorAuthenticationController@getById']);
    Route::post('/', ['middleware' => 'cors', 'uses' => 'TwoFactorAuthenticationController@create']);
    Route::delete('/', ['middleware' => 'cors', 'uses' => 'TwoFactorAuthenticationController@delete']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/sources-bot-profiles'], function()
{
    //sources-bot-profiles
    Route::get('/', ['middleware' => 'cors', 'uses' => 'SourcesBotProfilesController@get']);
    Route::get('/all', ['middleware' => 'cors', 'uses' => 'SourcesBotProfilesController@getAll']);
    Route::put('/', ['middleware' => 'cors', 'uses' => 'SourcesBotProfilesController@update']);
});

Route::group(['middleware' => ['auth:api'], 'prefix' => '/entity/source-bot-profile-status'], function()
{
    //source-bot-profile-status
    Route::get('/', ['middleware' => 'cors', 'uses' => 'SourceBotProfileStatusesController@get']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/rule-capture-regexp'], function() {
    //rule capture regexps
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'RuleCaptureRegexpController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'RuleCaptureRegexpController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'RuleCaptureRegexpController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'RuleCaptureRegexpController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'RuleCaptureRegexpController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/template-category'], function() {
    //Template categories
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'TemplateCategoryController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'TemplateCategoryController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'TemplateCategoryController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'TemplateCategoryController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'TemplateCategoryController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/condition-alltime'], function() {
    //all time conditions
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'ConditionAlltimeController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ConditionAlltimeController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'ConditionAlltimeController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'ConditionAlltimeController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'ConditionAlltimeController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/condition-nosence'], function() {
    //no sence conditions
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'ConditionNosenceController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'ConditionNosenceController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'ConditionNosenceController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'ConditionNosenceController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'ConditionNosenceController@delete']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => '/entity/nonresponse-condition-alltime'], function() {
    //all time non response conditions
    Route::get('/{id}', ['middleware' => 'cors', 'uses' => 'NonresponseConditionAlltimeController@getById']);
    Route::get('/', ['middleware' => 'cors', 'uses' => 'NonresponseConditionAlltimeController@get']);
    Route::post('/', ['middleware' => ['cors'], 'uses' => 'NonresponseConditionAlltimeController@create']);
    Route::put('/', ['middleware' => ['cors'], 'uses' => 'NonresponseConditionAlltimeController@update']);
    Route::delete('/', ['middleware' => ['cors'], 'uses' => 'NonresponseConditionAlltimeController@delete']);
});

//get dialogs
Route::get('/entity/dialog', ['middleware' => ['cors', 'auth:api'], 'uses' => 'DialogController@get']);


