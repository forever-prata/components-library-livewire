<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::all();

        $headers = ['Name', 'Description', 'Price', 'In Stock', 'Actions'];

        $rows = $produtos->map(function ($p) {
            return [
                $p->name,
                $p->description,
                $p->price,
                $p->in_stock ? 'Yes' : 'No',
                [
                    'show' => route('produtos.show', $p->id),
                    'edit' => route('produtos.edit', $p->id),
                    'delete' => route('produtos.destroy', $p->id),
                ]
            ];
        })->toArray();

        return view('produtos.index', compact('headers', 'rows'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
