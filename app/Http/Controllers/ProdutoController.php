<?php

// gerado automaticamente pela biblioteca

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        $collection = Produto::all();
        return view('produtos.index', compact('collection'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        $data = $request->all();
        $data['in_stock'] = $request->has('in_stock');

        Produto::create($data);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto created successfully.');
    }

    public function show(Produto $produto)
    {
        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
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
