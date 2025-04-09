<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Endereco;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadeController extends Controller
{
    public function index()
    {
        return response()->json(Unidade::paginate(10));
    }

    public function show($id)
    {
        $unidade = Unidade::with('enderecos.cidade')->firstOrFail();
        return response()->json($unidade);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'unid_nome' => 'required|string|max:200',
                'unid_sigla' => 'required|string|max:20',
                'end_tipo_logradouro' => 'required|string|max:20',
                'end_logradouro' => 'required|string|max:200',
                'end_numero' => 'required|integer',
                'end_bairro' => 'required|string|max:100',
                'cid_id' => 'nullable|exists:cidade,cid_id|required_without:cid_nome,cid_uf',
                'cid_nome' => 'nullable|string|max:200|required_with:cid_uf|required_without:cid_id',
                'cid_uf' => 'nullable|string|max:2|required_with:cid_nome|required_without:cid_id',
            ]);

            DB::beginTransaction();

            $cid_id = $data['cid_id'] ?? optional(
                Cidade::whereRaw('LOWER(cid_nome) = ? AND LOWER(cid_uf) = ?', [
                    strtolower($data['cid_nome']),
                    strtolower($data['cid_uf'])
                ])->first()
                ?? Cidade::create([
                'cid_nome' => $data['cid_nome'],
                'cid_uf' => $data['cid_uf'],
            ])
            )->cid_id;

            $unidade = Unidade::create([
                'unid_nome' => $data['unid_nome'],
                'unid_sigla' => $data['unid_sigla'],
            ]);

            $endereco = Endereco::create([
                'end_tipo_logradouro' => $data['end_tipo_logradouro'],
                'end_logradouro' => $data['end_logradouro'],
                'end_numero' => $data['end_numero'],
                'end_bairro' => $data['end_bairro'],
                'cid_id' => $cid_id,
            ]);

            $unidade->enderecos()->attach($endereco->end_id);

            DB::commit();
            $unidade->load('enderecos.cidade');
            return response()->json($unidade, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao cadastrar unidade: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'unid_nome' => 'required|string|max:200',
            'unid_sigla' => 'required|string|max:20',
            'end_tipo_logradouro' => 'required|string|max:20',
            'end_logradouro' => 'required|string|max:200',
            'end_numero' => 'required|integer',
            'end_bairro' => 'required|string|max:100',
            'cid_id' => 'nullable|exists:cidade,cid_id|required_without:cid_nome,cid_uf',
            'cid_nome' => 'nullable|string|max:200|required_with:cid_uf|required_without:cid_id',
            'cid_uf' => 'nullable|string|max:2|required_with:cid_nome|required_without:cid_id',
        ]);

        try {
            DB::beginTransaction();

            $cid_id = $validated['cid_id'] ?? optional(
                Cidade::whereRaw('LOWER(cid_nome) = ? AND LOWER(cid_uf) = ?', [
                    strtolower($validated['cid_nome']),
                    strtolower($validated['cid_uf'])
                ])->first()
                ?? Cidade::create([
                'cid_nome' => $validated['cid_nome'],
                'cid_uf' => $validated['cid_uf'],
            ])
            )->cid_id;

            $unidade = Unidade::findOrFail($id);
            $unidade->update([
                'unid_nome' => $validated['unid_nome'],
                'unid_sigla' => $validated['unid_sigla'],
            ]);
            $endereco = $unidade->enderecos->first();
            if ($endereco) {
                $endereco->update([
                    'end_tipo_logradouro' => $validated['end_tipo_logradouro'],
                    'end_logradouro' => $validated['end_logradouro'],
                    'end_numero' => $validated['end_numero'],
                    'end_bairro' => $validated['end_bairro'],
                    'cid_id' => $cid_id,
                ]);
            }
            DB::commit();
            $unidade->load('enderecos.cidade');
            return response()->json($unidade, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao atualizar unidade: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $unidade = Unidade::with('enderecos')->findOrFail($id);
            $endereco = $unidade->enderecos->first();

            $unidade->enderecos()->detach();
            $unidade->delete();
            if ($endereco) {
                $endereco->delete();
            }

            DB::commit();
            return response()->json(['message' => 'Unidade excluída com sucesso.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao excluir unidade: ' . $e->getMessage()], 500);
        }
    }
}
