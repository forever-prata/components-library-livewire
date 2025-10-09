<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $collection = Categoria::all();
        return view('categorias.index', compact('collection'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);


        Categoria::create($request->all());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria created successfully.');
    }

    public function show(Categoria $categoria)
    {

        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {

        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'name' => 'required',
        ]);


        $categoria->update($request->all());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria updated successfully.');
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoria deleted successfully.');
    }
}