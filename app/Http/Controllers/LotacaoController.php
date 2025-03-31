<?php

namespace App\Http\Controllers;

use App\Models\Lotacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LotacaoController extends Controller
{
    public function index()
    {
        return response()->json(
            Lotacao::paginate(10)
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pes_id' => ['required', 'exists:pessoa,pes_id'],
            'unid_id' => ['required', 'exists:unidade,unid_id'],
            'lot_data_lotacao' => ['required', 'date'],
            'lot_data_remocao' => ['nullable', 'date', 'after_or_equal:lot_data_lotacao'],
            'lot_portaria' => ['required', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Erro de validação.',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $lotacao = Lotacao::create($validator->validated());

            return response()->json([
                'message' => 'Lotação criada com sucesso.',
                'data' => $lotacao
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Erro interno ao tentar cadastrar a lotação.'
            ], 500);
        }
    }

    public function show($id)
    {
        return response()->json(
            Lotacao::findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $lotacao = Lotacao::find($id);

        if (!$lotacao) {
            return response()->json([
                'error' => 'Lotação não encontrada.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lot_data_lotacao' => ['sometimes', 'date'],
            'lot_data_remocao' => ['nullable', 'date', 'after_or_equal:lot_data_lotacao'],
            'lot_portaria' => ['sometimes', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Erro de validação.',
                'messages' => $validator->errors()
            ], 422);
        }

        try {
            $lotacao->update($validator->validated());

            return response()->json([
                'message' => 'Lotação atualizada com sucesso.',
                'data' => $lotacao
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro interno ao tentar atualizar a lotação.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        Lotacao::destroy($id);
        return response()->json(['message' => 'Lotação removida com sucesso']);
    }
}
