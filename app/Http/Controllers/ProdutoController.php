<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     * // Gerado automaticamente pela biblioteca
     */
    public function index()
    {
        $collection = Produto::all();
        return view('produtos.index', compact('collection'));
    }

    /**
     * Show the form for creating a new resource.
     * // Gerado automaticamente pela biblioteca
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     * // Gerado automaticamente pela biblioteca
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'in_stock' => 'required'
        ]);

        Produto::create($request->all());

        return redirect()->route('produtos.index')
            ->with('success', 'Produto created successfully.');
    }

    /**
     * Display the specified resource.
     * // Gerado automaticamente pela biblioteca
     */
    public function show(Produto $produto)
    {
        return view('produtos.show', compact('produto'));
    }

    /**
     * Show the form for editing the specified resource.
     * // Gerado automaticamente pela biblioteca
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     * // Gerado automaticamente pela biblioteca
     */
    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'in_stock' => 'required'
        ]);

        $produto->update($request->all());

        return redirect()->route('produtos.index')
            ->with('success', 'Produto updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * // Gerado automaticamente pela biblioteca
     */
    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto deleted successfully.');
    }
}