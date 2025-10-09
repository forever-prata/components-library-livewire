<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use App\Models\Produto;use App\Models\Categoria;

use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        $collection = Produto::with(['categoria'])->get();
        return view('produtos.index', compact('collection'));
    }

    public function create()
    {
        $categorias = Categoria::all();
        return view('produtos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $data = $request->all();
        $data['in_stock'] = $request->has('in_stock');

        Produto::create($data);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto created successfully.');
    }

    public function show(Produto $produto)
    {
        $produto->load(['categoria']);
        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        $produto->load(['categoria']);
        $categorias = Categoria::all();
        return view('produtos.edit', compact('produto', 'categorias'));
    }

    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $data = $request->all();
        $data['in_stock'] = $request->has('in_stock');

        $produto->update($data);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto updated successfully.');
    }

    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('success', 'Produto deleted successfully.');
    }
}