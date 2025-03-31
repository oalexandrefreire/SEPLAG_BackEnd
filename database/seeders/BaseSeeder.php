<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pessoa;
use App\Models\ServidorEfetivo;
use App\Models\ServidorTemporario;
use App\Models\Unidade;
use App\Models\Lotacao;

class BaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $unidades = Unidade::factory(3)->create();

        foreach ($unidades as $unidade) {

            $pessoasEfetivas = Pessoa::factory(5)->create();

            foreach ($pessoasEfetivas as $pessoa) {
                ServidorEfetivo::factory()->create([
                    'pes_id' => $pessoa->pes_id,
                ]);

                Lotacao::factory()->create([
                    'pes_id' => $pessoa->pes_id,
                    'unid_id' => $unidade->unid_id,
                ]);
            }

            $pessoasTemporarias = Pessoa::factory(3)->create();

            foreach ($pessoasTemporarias as $pessoa) {
                ServidorTemporario::factory()->create([
                    'pes_id' => $pessoa->pes_id,
                ]);
            }
        }
    }
}
