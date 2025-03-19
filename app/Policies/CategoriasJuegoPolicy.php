<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CategoriasJuego;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoriasJuegoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_categorias::juego');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CategoriasJuego $categoriasJuego): bool
    {
        return $user->can('view_categorias::juego');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_categorias::juego');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CategoriasJuego $categoriasJuego): bool
    {
        return $user->can('update_categorias::juego');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CategoriasJuego $categoriasJuego): bool
    {
        return $user->can('delete_categorias::juego');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_categorias::juego');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CategoriasJuego $categoriasJuego): bool
    {
        return $user->can('force_delete_categorias::juego');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_categorias::juego');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CategoriasJuego $categoriasJuego): bool
    {
        return $user->can('restore_categorias::juego');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_categorias::juego');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CategoriasJuego $categoriasJuego): bool
    {
        return $user->can('replicate_categorias::juego');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_categorias::juego');
    }
}
