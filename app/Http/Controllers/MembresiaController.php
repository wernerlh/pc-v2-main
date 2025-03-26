<?php

namespace App\Http\Controllers;

use App\Models\Membresia;
use Illuminate\Http\Request;

class MembresiaController extends Controller
{
    public function index()
    {
        $membresias = Membresia::all(); // Obtén todas las membresías sin filtrar por estado
        
        return view('membresias', compact('membresias'));
    }
}