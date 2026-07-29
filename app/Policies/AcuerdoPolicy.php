<?php

namespace App\Policies;

use App\Models\Acuerdo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AcuerdoPolicy
{
    /**
     * Determine if the user is an administrator.
     */
    private function isAdmin(User $user): bool
    {
        return $user->email === 'v.arochi@mapetzin.com' || 
               $user->email === 'gerencia_serv_gobierno@lesli.com.mx' || 
               $user->hasRole('Administrador');
    }

    /**
     * Determine if the user is a Director General (Read-only view for everything).
     */
    private function isDirector(User $user): bool
    {
        return $user->hasRole('Director General');
    }

    /**
     * Determine if the user has access to a specific area.
     */
    private function hasAccessToArea(User $user, string $area): bool
    {
        return in_array(strtolower($area), array_map('strtolower', $user->areas));
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Acuerdo $acuerdo): bool
    {
        if ($this->isAdmin($user) || $this->isDirector($user))
            return true;
        return $this->hasAccessToArea($user, $acuerdo->area) || strtolower($user->name) === strtolower($acuerdo->responsable);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // El director no crea acuerdos, solo ve
        if ($this->isDirector($user))
            return false;

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Acuerdo $acuerdo): bool
    {
        if ($this->isAdmin($user))
            return true;
        
        if ($this->isDirector($user))
            return false; // Solo consulta

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Acuerdo $acuerdo): bool
    {
        if ($this->isAdmin($user))
            return true;

        if ($this->isDirector($user))
            return false;

        return $this->hasAccessToArea($user, $acuerdo->area) || strtolower($user->name) === strtolower($acuerdo->responsable);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Acuerdo $acuerdo): bool
    {
        if ($this->isAdmin($user))
            return true;

        if ($this->isDirector($user))
            return false;

        return $this->hasAccessToArea($user, $acuerdo->area) || strtolower($user->name) === strtolower($acuerdo->responsable);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Acuerdo $acuerdo): bool
    {
        if ($this->isAdmin($user))
            return true;

        if ($this->isDirector($user))
            return false;

        return $this->hasAccessToArea($user, $acuerdo->area) || strtolower($user->name) === strtolower($acuerdo->responsable);
    }

}
