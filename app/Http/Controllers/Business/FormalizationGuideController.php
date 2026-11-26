<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FormalizationGuideController extends Controller
{
    /**
     * Muestra el centro educativo de tutoriales para formalización de empresas.
     */
    public function index()
    {
        $company = Auth::user()->company;

        if ($company && $company->isMexican()) {
            return redirect()->route('business.dashboard')
                ->with('info', 'La guía de formalización colombiana (Cámara de Comercio y DIAN) aplica únicamente para comercios en Colombia.');
        }

        return view('business.formalization.guide', compact('company'));
    }
}
