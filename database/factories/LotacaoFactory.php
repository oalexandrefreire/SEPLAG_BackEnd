<?php

namespace Database\Factories;

use App\Models\Lotacao;
use App\Models\Pessoa;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LotacaoFactory extends Factory
{
    protected $model = Lotacao::class;

    public function definition()
    {
        return [
            'pes_id' => fn() => Pessoa::factory()->create()->pes_id,
            'unid_id' => fn() => Unidade::factory()->create()->unid_id,
            'lot_data_lotacao' => now()->subYear(),
            'lot_data_remocao' => null,
            'lot_portaria' => 'Portaria ' . fake()->randomNumber(3),
        ];

    }
}
