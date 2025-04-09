<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use Illuminate\Http\Request;

class CidadeController extends Controller
{
    public function index()
    {
        return response()->json(Cidade::paginate(10));
    }

    public function show($id)
    {
        return response()->json(Cidade::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cid_nome' => 'required|string|max:200',
            'cid_uf' => 'required|string|max:2',
        ]);

        return response()->json(Cidade::create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $cidade = Cidade::findOrFail($id);
        $data = $request->validate([
            'cid_nome' => 'sometimes|required|string|max:200',
            'cid_uf' => 'sometimes|required|string|max:2',
        ]);
        $cidade->update($data);
        return response()->json($cidade);
    }

}
