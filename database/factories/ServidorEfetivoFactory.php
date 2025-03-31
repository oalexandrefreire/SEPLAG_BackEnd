<?php

namespace Database\Factories;

use App\Models\ServidorEfetivo;
use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServidorEfetivoFactory extends Factory
{
    protected $model = ServidorEfetivo::class;

    public function definition()
    {
        return [
            'pes_id' => fn() => Pessoa::factory()->create()->pes_id,
            'se_matricula' => $this->faker->unique()->numerify('#######'),
        ];
    }
}
