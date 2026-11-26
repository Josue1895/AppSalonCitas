<?php

namespace App\Http\Controllers;

use App\Models\Service;

class HomeController extends Controller
{
    public function index()
    {
        $servicios = Service::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('landing', compact('servicios'));
    }
}
