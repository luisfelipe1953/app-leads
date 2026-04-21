<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre'           => $this->faker->name(),
            'email'            => $this->faker->unique()->safeEmail(),
            'telefono'         => $this->faker->optional()->numerify('+57300#######'),
            'fuente'           => $this->faker->randomElement(['instagram', 'facebook', 'landing_page', 'referido', 'otro']),
            'producto_interes' => $this->faker->optional()->randomElement([
                'Curso de Marketing Digital',
                'Mentoria 1:1',
                'Pack Redes Sociales',
                'Consultoria Empresarial',
                'Curso de Copywriting',
            ]),
            'presupuesto' => $this->faker->optional()->randomFloat(2, 50, 5000),
        ];
    }
}
