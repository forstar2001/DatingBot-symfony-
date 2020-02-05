<?php


namespace App\Helpers;


use App\Models\RoleCategorie;
use App\Models\RoleDefinition;

class RoleCategoryHelper
{
    public static function setScenariosPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Scenarios')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setConditionsPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Conditions')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setNonResponseConditionsPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Non-response Conditions')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setTranslationsPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Translations')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setTranslationsContentPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Translations Content')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setDictionariesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Dictionaries')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setSourcesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Sources')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setCountriesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Countries')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setCitiesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Cities')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setRegionsPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Regions')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setProfileDetailTypesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Profile detail types')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setBotProfilesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null, $statusId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Bot profiles')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId, $statusId);
    }

    public static function setBotProfileDetailsPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null, $statusId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Bot Profile Details')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId, $statusId);
    }

    public static function setLanguagesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Languages')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setProfileDetailValueTypesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Profile Detail Value Types')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setRolesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Roles')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setUsersPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Users')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setAssigningRolesToUserPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Assigning Roles to User')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    public static function setTestScenariosPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Test Scenarios')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }
    public static function setRulesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Rules')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }
    public static function setSourcesBotProfilesPermissions($role, $view, $edit = null, $delete = null, $create = null, $countryId = null)
    {
        $categoryId = RoleCategorie::where('name', 'Sources BotProfiles')->first()->id;
        self::setPermissions($role, $categoryId, $view, $edit, $delete, $create, $countryId);
    }

    private static function setPermissions($role, $categoryId, $view, $edit = null, $delete = null, $create = null, $countryId = null, $statusId = null)
    {
        if (!isset($role) || !isset($categoryId) || !isset($view)) {
            throw new \Exception('Error');
        }

        try {

            $roleDefinition = new RoleDefinition();
            $roleDefinition->action_id = $view;
            $roleDefinition->category_id = $categoryId;
            if (isset($countryId)) {
                $roleDefinition->country_id = $countryId;
            }
            if (isset($statusId)) {
                $roleDefinition->profile_status_type_id = $statusId;
            }
            $role->role_definitions()->save($roleDefinition);

            if (isset($edit)) {
                $roleDefinition = new RoleDefinition();
                $roleDefinition->action_id = $edit;
                $roleDefinition->category_id = $categoryId;
                if (isset($countryId)) {
                    $roleDefinition->country_id = $countryId;
                }
                if (isset($statusId)) {
                    $roleDefinition->profile_status_type_id = $statusId;
                }
                $role->role_definitions()->save($roleDefinition);
            }
            if (isset($delete)) {
                $roleDefinition = new RoleDefinition();
                $roleDefinition->action_id = $delete;
                $roleDefinition->category_id = $categoryId;
                if (isset($countryId)) {
                    $roleDefinition->country_id = $countryId;
                }
                if (isset($statusId)) {
                    $roleDefinition->profile_status_type_id = $statusId;
                }
                $role->role_definitions()->save($roleDefinition);
            }
            if (isset($create)) {
                $roleDefinition = new RoleDefinition();
                $roleDefinition->action_id = $create;
                $roleDefinition->category_id = $categoryId;
                if (isset($countryId)) {
                    $roleDefinition->country_id = $countryId;
                }
                if (isset($statusId)) {
                    $roleDefinition->profile_status_type_id = $statusId;
                }
                $role->role_definitions()->save($roleDefinition);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}