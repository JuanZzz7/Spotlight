<?php

namespace App\Policies;

use App\Models\ElectronicDocument;
use App\Models\User;

class ElectronicDocumentPolicy
{
    /**
     * Determina si el usuario puede ver la lista de documentos.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isBusiness();
    }

    /**
     * Determina si el usuario puede ver un documento específico.
     */
    public function view(User $user, ElectronicDocument $document): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // El comercio dueño del documento
        if ($user->isBusiness() && $user->company && $user->company->id === $document->company_id) {
            return true;
        }

        // El cliente adquirente del documento
        if ($document->user_id && $user->id === $document->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determina si el usuario puede emitir documentos electrónicos.
     */
    public function create(User $user): bool
    {
        if ($user->isAdmin()) {
            return false;
        }

        return $user->isBusiness()
            && $user->company !== null
            && $user->company->canIssueElectronicInvoices();
    }

    /**
     * Determina si el usuario puede emitir notas crédito sobre el documento.
     */
    public function creditNote(User $user, ElectronicDocument $document): bool
    {
        if (!$document->isValidated()) {
            return false;
        }

        return $user->isBusiness()
            && $user->company !== null
            && $user->company->id === $document->company_id
            && $user->company->canIssueElectronicInvoices();
    }

    /**
     * Determina si el usuario puede descargar los archivos XML/PDF del documento.
     */
    public function download(User $user, ElectronicDocument $document): bool
    {
        return $this->view($user, $document);
    }
}
