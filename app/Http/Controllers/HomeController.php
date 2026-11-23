<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $companies = Company::where('status', 'active')->latest()->take(6)->get();
        $products  = Product::where('active', true)->where('featured', true)
                        ->whereHas('company', function ($query) {
                            $query->where('status', 'active');
                        })
                        ->with('company')->latest()->get();
        return view('home.index', compact('companies', 'products'));
    }
}
