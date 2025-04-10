<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Endereco;
use App\Models\Lotacao;
use App\Models\Pessoa;
use App\Models\PessoaEndereco;
use App\Models\ServidorTemporario;
use App\Services\FotoPessoaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServidorTemporarioController extends Controller
{
    public function index()
    {
        return response()->json(ServidorTemporario::paginate(10));
    }

    public function show($id, FotoPessoaService $fotoPessoaService)
    {
        $servidor = ServidorTemporario::with([
            'pessoa',
            'pessoa.enderecos.cidade',
            'pessoa.fotos'
        ])->findOrFail($id);

        $fotos = $servidor->pessoa->fotos ?? [];

        $links = collect($fotos)->map(function ($foto) use ($fotoPessoaService) {
            return $fotoPessoaService->generatePresignedUrl($foto->fp_hash);
        });

        return response()->json([
            'servidor_temporario' => $servidor,
            'fotos_links_temporarios' => $links,
        ], 200);
    }

    public function store(Request $request, FotoPessoaService $fotoPessoaService)
    {
        try {
            $validated = $request->validate([
                'pes_nome' => 'required|string|max:200',
                'pes_data_nascimento' => 'required|date',
                'pes_sexo' => 'required|string|max:9',
                'pes_mae' => 'nullable|string|max:200',
                'pes_pai' => 'nullable|string|max:200',
                'st_data_admissao' => 'required|date',
                'st_data_demissao' => 'nullable|date|after_or_equal:st_data_admissao',
                'end_tipo_logradouro' => 'required|string|max:100',
                'end_logradouro' => 'required|string|max:200',
                'end_numero' => 'required|string|max:20',
                'end_bairro' => 'required|string|max:100',
                'cid_id' => 'nullable|exists:cidade,cid_id|required_without:cid_nome,cid_uf',
                'cid_nome' => 'nullable|string|max:200|required_with:cid_uf|required_without:cid_id',
                'cid_uf' => 'nullable|string|max:2|required_with:cid_nome|required_without:cid_id',
                'fotos' => 'nullable|array',
                'fotos.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ]);

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

            $pessoa = Pessoa::create([
                'pes_nome' => $validated['pes_nome'],
                'pes_data_nascimento' => $validated['pes_data_nascimento'],
                'pes_sexo' => $validated['pes_sexo'],
                'pes_mae' => $validated['pes_mae'] ?? null,
                'pes_pai' => $validated['pes_pai'] ?? null,
            ]);

            $endereco = Endereco::create([
                'end_tipo_logradouro' => $validated['end_tipo_logradouro'],
                'end_logradouro' => $validated['end_logradouro'],
                'end_numero' => $validated['end_numero'],
                'end_bairro' => $validated['end_bairro'],
                'cid_id' => $cid_id,
            ]);

            PessoaEndereco::create([
                'pes_id' => $pessoa->pes_id,
                'end_id' => $endereco->end_id,
            ]);

            $servidor = ServidorTemporario::create([
                'pes_id' => $pessoa->pes_id,
                'st_data_admissao' => $validated['st_data_admissao'],
                'st_data_demissao' => $validated['st_data_demissao'] ?? null,
            ]);

            if ($request->hasFile('fotos')) {
                $fotoPessoaService->uploadFotos($pessoa->pes_id, $request->file('fotos'));
            }

            DB::commit();

            $servidor->load('pessoa', 'pessoa.enderecos.cidade', 'pessoa.fotos');

            $links = [];
            foreach ($servidor->pessoa->fotos as $foto) {
                $links[] = $fotoPessoaService->generatePresignedUrl($foto->fp_hash);
            }

            return response()->json(['servidor_temporario' => $servidor, 'fotos_links_temporarios' => $links], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao criar servidor: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'pes_nome' => 'required|string|max:200',
                'pes_data_nascimento' => 'required|date',
                'pes_sexo' => 'required|string|max:9',
                'pes_mae' => 'nullable|string|max:200',
                'pes_pai' => 'nullable|string|max:200',
                'st_data_admissao' => 'sometimes|date',
                'st_data_demissao' => 'nullable|date|after_or_equal:st_data_admissao',
                'end_tipo_logradouro' => 'required|string|max:100',
                'end_logradouro' => 'required|string|max:200',
                'end_numero' => 'required|string|max:20',
                'end_bairro' => 'required|string|max:100',
                'cid_id' => 'nullable|exists:cidade,cid_id|required_without:cid_nome,cid_uf',
                'cid_nome' => 'nullable|string|max:200|required_with:cid_uf|required_without:cid_id',
                'cid_uf' => 'nullable|string|max:2|required_with:cid_nome|required_without:cid_id',
            ]);

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

            $pessoa = Pessoa::findOrFail($id);
            $pessoa->update([
                'pes_nome' => $validated['pes_nome'],
                'pes_data_nascimento' => $validated['pes_data_nascimento'],
                'pes_sexo' => $validated['pes_sexo'],
                'pes_mae' => $validated['pes_mae'] ?? null,
                'pes_pai' => $validated['pes_pai'] ?? null,
            ]);

            $endereco = Endereco::updateOrCreate(
                ['end_id' => $pessoa->enderecos->first()->end_id],
                [
                    'end_tipo_logradouro' => $validated['end_tipo_logradouro'],
                    'end_logradouro' => $validated['end_logradouro'],
                    'end_numero' => $validated['end_numero'],
                    'end_bairro' => $validated['end_bairro'],
                    'cid_id' => $cid_id,
                ]
            );

            PessoaEndereco::updateOrCreate(
                ['pes_id' => $pessoa->pes_id],
                ['end_id' => $endereco->end_id]
            );

            $servidorTemporario = ServidorTemporario::findOrFail($id);
            $servidorTemporario->update([
                'st_data_admissao' => $validated['st_data_admissao'],
                'st_data_demissao' => $validated['st_data_demissao'] ?? null,
            ]);

            DB::commit();

            $servidorTemporario->load('pessoa', 'pessoa.enderecos.cidade');

            return response()->json(['servidor_temporario' => $servidorTemporario], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erro ao atualizar servidor: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id, FotoPessoaService $fotoPessoaService)
    {
        try {
            DB::beginTransaction();
            $servidor = ServidorTemporario::findOrFail($id);
            $pessoaId = $servidor->pes_id;
            Lotacao::where('pes_id', $pessoaId)->delete();
            $fotoPessoaService->deleteFotosByPessoaId($id);
            $pessoaEnderecos = PessoaEndereco::where('pes_id', $pessoaId)->get();
            PessoaEndereco::where('pes_id', $pessoaId)->delete();
            foreach ($pessoaEnderecos as $pessoaEndereco) {
                Endereco::where('end_id', $pessoaEndereco->end_id)->delete();
            }
            Pessoa::where('pes_id', $pessoaId)->delete();
            $servidor->delete();
            DB::commit();
            return response()->json(['message' => 'Servidor removido com sucesso']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao excluir servidor: ' . $e->getMessage()]);
        }
    }
}
