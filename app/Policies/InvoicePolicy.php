<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Admins peuvent tout faire.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->is_admin) {
            return true;
        }

        return null;
    }

    /**
     * Tout utilisateur connecté peut créer (il sera le créateur).
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Voir une facture : uniquement si on en est le créateur (ou admin via before).
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return (int) $invoice->created_by === (int) $user->id;
    }

    /**
     * Modifier une facture : uniquement si on en est le créateur (ou admin via before).
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return (int) $invoice->created_by === (int) $user->id;
    }
}
