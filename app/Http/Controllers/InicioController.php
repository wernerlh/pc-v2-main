<?php

namespace App\Http\Controllers;

use App\Models\Portada;
use App\Models\JuegosOnline;
use Illuminate\Http\Request;


class InicioController extends Controller
{
    public function index()
    {
        // Obtener las portadas activas ordenadas por el campo orden
        $portadas = Portada::where('estado', 'activo')
                          ->orderBy('orden')
                          ->get();
        
        // Obtener juegos agrupados por categorías
        $juegos = JuegosOnline::with('categoria')
            ->where('estado', 'activo')
            ->get();
            
        // Organizar juegos por categoría
        $categorias = [];
        foreach ($juegos as $juego) {
            $categoria = $juego->categoria->nombre ?? 'General';
            
            if (!isset($categorias[$categoria])) {
                $categorias[$categoria] = [];
            }
            
            $categorias[$categoria][] = $juego;
        }
        
        // Limitar a 2 juegos por categoría para la página de inicio
        foreach ($categorias as $categoria => $juegosEnCategoria) {
            $categorias[$categoria] = array_slice($juegosEnCategoria, 0, 2);
        }
        
        return view('inicio', compact('portadas', 'categorias'));
    }
}