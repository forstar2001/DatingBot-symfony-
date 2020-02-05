<?php

namespace App\Policies;

use App\User;
use App\Models\Scenario;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScenarioPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the scenario.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Scenario  $scenario
     * @return mixed
     */
    public function view(User $user, Scenario $scenario)
    {
        return true;
    }

    /**
     * Determine whether the user can create scenarios.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the scenario.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Scenario  $scenario
     * @return mixed
     */
    public function update(User $user, Scenario $scenario)
    {
        return $user->id == $scenario->user_id;
    }

    /**
     * Determine whether the user can delete the scenario.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Scenario  $scenario
     * @return mixed
     */
    public function delete(User $user, Scenario $scenario)
    {
        return $user->id == $scenario->user_id;
    }
}
