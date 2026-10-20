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

        return view('business.formalization.guide', compact('company'));
    }
}
