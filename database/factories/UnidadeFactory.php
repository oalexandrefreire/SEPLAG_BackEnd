<?php

namespace Database\Factories;

use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadeFactory extends Factory
{
    protected $model = Unidade::class;

    public function definition()
    {
        return [
            'unid_nome' => $this->faker->word,
            'unid_sigla' => $this->faker->word,
        ];
    }
}
