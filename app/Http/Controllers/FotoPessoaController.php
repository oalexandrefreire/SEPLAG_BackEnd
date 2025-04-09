<?php

namespace App\Http\Controllers;

use App\Models\FotoPessoa;
use App\Services\FotoPessoaService;
use Illuminate\Http\Request;

class FotoPessoaController extends Controller
{

    public function show($pes_id, FotoPessoaService $fotoPessoaService)
    {
        try {
            $fotos = FotoPessoa::where('pes_id', $pes_id)->get();

            if ($fotos->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma foto encontrada para o pes_id fornecido.',
                ], 404);
            }

            $links = $fotos->map(function ($foto) use ($fotoPessoaService) {
                return $fotoPessoaService->generatePresignedUrl($foto->fp_hash);
            });

            return response()->json([
                'links_temporarios' => $links,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro inesperado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function upload($pes_id, Request $request, FotoPessoaService $fotoPessoaService)
    {
        try {
            $result = $fotoPessoaService->uploadFotos($pes_id, $request->file('fotos'));

            if (!$result['success']) {
                return response()->json([
                    'message' => 'Erro de validação',
                    'errors' => $result['errors'],
                ], 422);
            }

            return response()->json([
                'links_temporarios' => $result['links'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro inesperado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
