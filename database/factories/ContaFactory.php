<?php

namespace Database\Factories;

use App\Models\Conta as Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContaFactory extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
        return [
            'credencial' => sha1(str()->uuid()),
            'chave' => sha1(str()->uuid()),
            'nome' => $this->faker->name(),
            'cpfcnpj' => $this->faker->numberBetween(90000000000, 99999999999),
            'email' => $this->faker->safeEmail(),
            'telefone' => $this->faker->numberBetween(90000000000, 99999999999),
        ];
    }
}
