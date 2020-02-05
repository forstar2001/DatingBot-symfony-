<?php

namespace App\Services;

use App\Models\BotProfile;
use App\Models\Country;
use App\Models\ProfileStatusType;
use App\Models\RoleCategorie;
use App\Models\RoleAction;
use App\Models\RoleDefinition;
use Illuminate\Database\Eloquent\Builder;
use Reliese\Database\Eloquent\Model as Eloquent;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class RoleCheckingHelper
{
    public static function filterDataByViewRoles(Builder $data,
                                                 RoleCategorie $category,
                                                  $customFilterPrefix = NULL,
                                                  $dataAsCountriesItself = NULL) {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $data;
        }

        $roles = $user->roles()
            ->with(['role', 'role.role_definitions',
            'role.role_definitions.category' => function($q) use ($category) {
                $q->where('name', $category->name);
            },
            'role.role_definitions.action' => function ($q) {
                $q->whereIn('name', ['view all', 'view own']);
            },
            'role.role_definitions.country',
            'role.role_definitions.profile_status_type'])
            ->get();

        $filterByCountry = true;
        $filterByUser = true;
        $filterByStatus = true;
        $countries = collect([]);
        $statuses = collect([]);
        foreach ($roles as $role) {

            //user can view all records, no need to check next roles
            if (!$filterByCountry && !$filterByStatus && !$filterByUser) {
                break;
            }

            foreach ($role->role()->first()->role_definitions()->get() as $definition) {

                //user can view all records, no need to check next role definitions
                if (!$filterByCountry && !$filterByStatus && !$filterByUser) {
                    break;
                }

                if ($definition->category()->first()->name != $category->name
                    || !in_array( $definition->action()->first()->name, ['view all', 'view own'])) {
                    continue;
                }

                if  ($definition->action()->first()->name == 'view all') {
                    $filterByUser = false;
                }

                if ($definition->country_id == null) {
                    $filterByCountry = false;
                } else {
                    $country = Country::find($definition->country_id);
                    $countries->push($country);
                }

                if ($definition->profile_status_type_id == NULL) {
                    $filterByStatus = false;
                } else {
                    $status = ProfileStatusType::find($definition->profile_status_type_id);
                    $statuses->push($status);
                }

            }
        }

        if ($roles->count() == 0) {
            return collect([]);
        }


        if ($filterByCountry) {
            if ($dataAsCountriesItself) {
                $data = $data->whereIn('id', $countries->pluck('id')->toArray());
            } else {
                if (empty($customFilterPrefix)) {
                    $data = $data->whereIn('country_id', $countries->pluck('id')->toArray());
                } else {
                    $data = $data->whereHas($customFilterPrefix, function($q) use ($countries, $customFilterPrefix) {
                        $q->whereIn($customFilterPrefix . 's.country_id', $countries->pluck('id')->toArray());
                    });
                }
            }
        }
        if ($filterByUser) {
            if (empty($customFilterPrefix)) {
                $data = $data->where('user_id', $user->id);
            } else {
                $data = $data->whereHas($customFilterPrefix, function($q) use ($user, $customFilterPrefix) {
                   $q->where($customFilterPrefix . 's.user_id', $user->id);
                });
            }
        }
        if ($filterByStatus) {
            if (empty($customFilterPrefix)) {
                $data = $data->whereIn('status', $statuses->pluck('name')->toArray());
            } else {
                $data = $data->whereHas($customFilterPrefix, function($q) use ($statuses, $customFilterPrefix) {
                    $q->whereIn($customFilterPrefix . 's.status', $statuses->pluck('id')->toArray());
                });
            }
        }

        return $data;
    }

    public static function checkIfActionAuthorized(?Eloquent $model, RoleCategorie $category,
                                                   RoleAction $action, bool $modelAsCountryItself = NULL) {

        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        }

        $roles = $user->roles()->with(['role', 'role.role_definitions',
            'role.role_definitions.category' => function ($q) use ($category) {
                $q->where('name', $category->name);
            },
            'role.role_definitions.action',
            'role.role_definitions.country',
            'role.role_definitions.profile_status_type'])
            ->get();

        $grantedByCountry = false;
        $grantedByUser = false;
        $grantedByStatus = false;
        $grantedByAction = false;
        foreach ($roles as $role) {

            //user has permission, no need to check next roles
            if ($grantedByCountry && $grantedByStatus && $grantedByUser && $grantedByAction) {
                break;
            }

            foreach ($role->role()->first()->role_definitions()->get() as $definition) {

                //user has permission, no need to check next role definitions
                if ($grantedByCountry && $grantedByStatus && $grantedByUser && $grantedByAction) {
                    break;
                }

                $definitionCategory = $definition->category()->first()->name;
                $definitionAction = $definition->action()->first()->name;
                $categoryName = $category->name;
                $actionName = $action->name;

                if ($definitionCategory != $categoryName
                    || ($definitionAction != $actionName
                        && !($definitionAction == 'view own' && $actionName == 'view all')
                        && !($definitionAction == 'remove own' && $actionName == 'remove all')
                        && !($definitionAction == 'edit own' && $actionName == 'edit all')
                    )) {
                    continue;
                }



                $grantedByAction = true;

                if ($action->name == 'view all') {

                    if ($definition->action()->first()->name == 'view all' || $definition->action()->first()->name == 'view own') {
                        $grantedByCountry = true;
                        $grantedByUser = true;
                        $grantedByStatus = true;
                    }

                } else if ($action->name == 'create') {

                    if (isset($model->country_id)) {
                        if ($definition->country_id == NULL || $definition->country_id == $model->country_id) {
                            $grantedByCountry = true;
                        }
                    } else if ($modelAsCountryItself) {
                        if ($definition->country_id == NULL || $definition->country_id == $model->id) {
                            $grantedByCountry = true;
                        }
                    } else {
                        $grantedByCountry = true;
                    }

                    if (isset($model->user_id)) {
                        if ($definition->action()->first()->name == 'create') {
                            $grantedByUser = true;
                        } else {
                            if ($model->user_id == $user->id) {
                                $grantedByUser = true;
                            }
                        }
                    } else {
                        $grantedByUser = true;
                    }

                    if (isset($model->status)) {
                        if (!isset($definition->profile_status_type()->first()->name)){
                            $grantedByStatus = true;
                        }elseif ($definition->profile_status_type()->first()->name == $model->status){
                            $grantedByStatus = true;
                        }
                    } else {
                        $grantedByStatus = true;
                    }

                } else if ($action->name == 'remove all') {

                    if (isset($model->country_id)) {
                        if ($definition->country_id == NULL || $definition->country_id == $model->country_id) {
                            $grantedByCountry = true;
                        }
                    }  else if ($modelAsCountryItself) {
                        if ($definition->country_id == NULL || $definition->country_id == $model->id) {
                            $grantedByCountry = true;
                        }
                    }
                    else {
                        $grantedByCountry = true;
                    }

                    if (isset($model->user_id)) {
                        if ($definition->action()->first()->name == 'remove all') {
                            $grantedByUser = true;
                        } else {
                            if ($model->user_id == $user->id) {
                                $grantedByUser = true;
                            }
                        }
                    } else {
                        $grantedByUser = true;
                    }

                    if (isset($model->status)) {
                        if (!isset($definition->profile_status_type()->first()->name)){
                            $grantedByStatus = true;
                        }elseif ($definition->profile_status_type()->first()->name == $model->status){
                            $grantedByStatus = true;
                        }
                    } else {
                        $grantedByStatus = true;
                    }

                } else if ($action->name == 'edit all') {

                    if (isset($model->country_id)) {
                        if ($definition->country_id == NULL || $definition->country_id == $model->country_id) {
                            $grantedByCountry = true;
                        }
                    } else if ($modelAsCountryItself) {
                        if ($definition->country_id == NULL || $definition->country_id == $model->id) {
                            $grantedByCountry = true;
                        }
                    } else {
                        $grantedByCountry = true;
                    }

                    if (isset($model->user_id)) {
                        if ($definition->action()->first()->name == 'edit all') {
                            $grantedByUser = true;
                        } else {
                            if ($model->user_id == $user->id) {
                                $grantedByUser = true;
                            }
                        }
                    } else {
                        $grantedByUser = true;
                    }

                    if (isset($model->status)) {
                        if (!isset($definition->profile_status_type()->first()->name)){
                            $grantedByStatus = true;
                        }elseif ($definition->profile_status_type()->first()->name == $model->status){
                            $grantedByStatus = true;
                        }
                    } else {
                        $grantedByStatus = true;
                    }

                }

            }
        }

        return ($grantedByUser && $grantedByStatus && $grantedByCountry && $grantedByAction);
    }



}