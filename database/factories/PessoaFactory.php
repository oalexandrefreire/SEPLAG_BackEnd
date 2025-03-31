<?php

namespace Database\Factories;

use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

class PessoaFactory extends Factory
{
    protected $model = Pessoa::class;

    public function definition()
    {
        return [
            'pes_nome' => $this->faker->name(),
            'pes_data_nascimento' => $this->faker->date('Y-m-d', '-20 years'),
            'pes_sexo' => $this->faker->randomElement(['Masculino', 'Feminino']),
            'pes_mae' => $this->faker->name('female'),
            'pes_pai' => $this->faker->name('male'),
        ];
    }
}
