<?php

namespace App\Policies;

use App\Models\CompanyFiscalProfile;
use App\Models\User;

class CompanyFiscalProfilePolicy
{
    public function view(User $user, CompanyFiscalProfile $profile): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isBusiness() && $user->company && $user->company->id === $profile->company_id;
    }

    public function update(User $user, CompanyFiscalProfile $profile): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // El comercio puede editar si no está habilitado o si está rechazado/pendiente de documentación
        return $user->isBusiness()
            && $user->company
            && $user->company->id === $profile->company_id
            && in_array($profile->fiscal_status, [
                CompanyFiscalProfile::STATUS_NOT_REQUESTED,
                CompanyFiscalProfile::STATUS_PENDING_DOCUMENTATION,
                CompanyFiscalProfile::STATUS_REJECTED,
                CompanyFiscalProfile::STATUS_IN_REVIEW,
            ], true);
    }

    public function verify(User $user): bool
    {
        return $user->isAdmin();
    }
}
