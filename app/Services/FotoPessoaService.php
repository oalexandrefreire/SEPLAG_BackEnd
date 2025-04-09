<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use App\Models\FotoPessoa;
use Illuminate\Support\Facades\Validator;

class FotoPessoaService
{
    public function uploadFotos($pes_id, $fotos)
    {
        $validator = Validator::make([
            'pes_id' => $pes_id,
            'fotos' => $fotos,
        ], [
            'pes_id' => 'required|exists:pessoa,pes_id',
            'fotos' => 'required|array',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            if (is_array($fotos)) {
                foreach ($fotos as $index => $foto) {
                    $fieldKey = "fotos.$index";
                    if (isset($errors[$fieldKey])) {
                        $originalName = $foto->getClientOriginalName();
                        $errors[$fieldKey] = "O arquivo \"{$originalName}\" falhou no upload ou não é válido.";
                    }
                }
            }

            return ['success' => false, 'errors' => $errors];
        }

        $links = [];

        foreach ($fotos as $foto) {
            $path = $foto->store('fotos', 'minio');

            $signedUrl = $this->generatePresignedUrl($path);

            FotoPessoa::create([
                'pes_id' => $pes_id,
                'fp_data' => now(),
                'fp_bucket' => env('MINIO_BUCKET'),
                'fp_hash' => $path,
            ]);

            $links[] = $signedUrl;
        }

        return ['success' => true, 'links' => $links];
    }

    public function generatePresignedUrl(string $path): string
    {
        $client = new S3Client([
            'region' => env('MINIO_REGION', 'us-east-1'),
            'version' => 'latest',
            'endpoint' => env('MINIO_PUBLIC_URL'),
            'credentials' => [
                'key' => env('MINIO_KEY'),
                'secret' => env('MINIO_SECRET'),
            ],
            'use_path_style_endpoint' => true,
        ]);

        try {
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => env('MINIO_BUCKET'),
                'Key' => $path,
            ]);

            $request = $client->createPresignedRequest($cmd, '+5 minutes');

            return (string)$request->getUri();
        } catch (AwsException $e) {
            throw new \Exception("Erro ao gerar URL: " . $e->getAwsErrorMessage());
        }
    }

    public function deleteFotosByPessoaId($pes_id)
    {
        $fotos = FotoPessoa::where('pes_id', $pes_id)->get();

        if (!$fotos->isEmpty()) {
            foreach ($fotos as $foto) {
                Storage::disk('minio')->delete($foto->fp_hash);
                $foto->delete();
            }
        }
    }
}
