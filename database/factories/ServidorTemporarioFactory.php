<?php

namespace Database\Factories;

use App\Models\ServidorTemporario;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServidorTemporarioFactory extends Factory
{
    protected $model = ServidorTemporario::class;

    public function definition()
    {
        return [
            'pes_id' => fn() => Pessoa::factory()->create()->pes_id,
            'st_data_admissao' => now()->subMonths(3),
            'st_data_demissao' => null,
        ];
    }
}
