<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BilleteraCliente;
use Illuminate\Auth\Access\HandlesAuthorization;

class BilleteraClientePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_billetera::cliente');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BilleteraCliente $billeteraCliente): bool
    {
        return $user->can('view_billetera::cliente');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_billetera::cliente');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BilleteraCliente $billeteraCliente): bool
    {
        return $user->can('update_billetera::cliente');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BilleteraCliente $billeteraCliente): bool
    {
        return $user->can('delete_billetera::cliente');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_billetera::cliente');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, BilleteraCliente $billeteraCliente): bool
    {
        return $user->can('force_delete_billetera::cliente');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_billetera::cliente');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, BilleteraCliente $billeteraCliente): bool
    {
        return $user->can('restore_billetera::cliente');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_billetera::cliente');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, BilleteraCliente $billeteraCliente): bool
    {
        return $user->can('replicate_billetera::cliente');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_billetera::cliente');
    }
}
