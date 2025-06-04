<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatStyleShop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatStyleShopController extends Controller
{
    public function index()
    {
        $categories = CatStyleShop::orderBy('nome')->get();
        return view('admin.cat-style-shop.index', compact('categories'));
    }

    public function create()
    {
        // Simplificado: não usamos mais hierarquia de categorias
        return view('admin.cat-style-shop.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|max:255|unique:cat_style_shop,nome',
        ]);

        CatStyleShop::create([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome),
        ]);

        return redirect()->route('admin.cat-style-shop.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(CatStyleShop $catStyleShop)
    {
        // Simplificado: não usamos mais hierarquia de categorias
        return view('admin.cat-style-shop.edit', compact('catStyleShop'));
    }

    public function update(Request $request, CatStyleShop $catStyleShop)
    {
        $request->validate([
            'nome' => 'required|max:255|unique:cat_style_shop,nome,' . $catStyleShop->id,
        ]);
        
        $catStyleShop->update([
            'nome' => $request->nome,
            'slug' => Str::slug($request->nome),
        ]);

        return redirect()->route('admin.cat-style-shop.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(CatStyleShop $catStyleShop)
    {
        $catStyleShop->delete();
        return redirect()->route('admin.cat-style-shop.index')->with('success', 'Categoria excluída com sucesso!');
    }
}
