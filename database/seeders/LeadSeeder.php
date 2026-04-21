<?php

namespace Database\Seeders;

use App\Models\Lead;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $leads = [
            [
                'nombre'           => 'María González',
                'email'            => 'maria.gonzalez@example.com',
                'telefono'         => '+573001234567',
                'fuente'           => 'instagram',
                'producto_interes' => 'Curso de Marketing Digital',
                'presupuesto'      => 500.00,
                'created_at'       => Carbon::now()->subDays(1),
            ],
            [
                'nombre'           => 'Carlos Rodríguez',
                'email'            => 'carlos.rodriguez@example.com',
                'telefono'         => '+573109876543',
                'fuente'           => 'facebook',
                'producto_interes' => 'Mentoría 1:1',
                'presupuesto'      => 1200.00,
                'created_at'       => Carbon::now()->subDays(2),
            ],
            [
                'nombre'           => 'Ana Martínez',
                'email'            => 'ana.martinez@example.com',
                'telefono'         => null,
                'fuente'           => 'landing_page',
                'producto_interes' => 'Pack Redes Sociales',
                'presupuesto'      => 300.00,
                'created_at'       => Carbon::now()->subDays(3),
            ],
            [
                'nombre'           => 'Luis Pérez',
                'email'            => 'luis.perez@example.com',
                'telefono'         => '+573152345678',
                'fuente'           => 'referido',
                'producto_interes' => 'Consultoría Empresarial',
                'presupuesto'      => 2500.00,
                'created_at'       => Carbon::now()->subDays(4),
            ],
            [
                'nombre'           => 'Sofía Torres',
                'email'            => 'sofia.torres@example.com',
                'telefono'         => '+573003456789',
                'fuente'           => 'instagram',
                'producto_interes' => 'Curso de Copywriting',
                'presupuesto'      => 450.00,
                'created_at'       => Carbon::now()->subDays(5),
            ],
            [
                'nombre'           => 'Andrés López',
                'email'            => 'andres.lopez@example.com',
                'telefono'         => null,
                'fuente'           => 'facebook',
                'producto_interes' => null,
                'presupuesto'      => null,
                'created_at'       => Carbon::now()->subDays(6),
            ],
            [
                'nombre'           => 'Valentina Díaz',
                'email'            => 'valentina.diaz@example.com',
                'telefono'         => '+573114567890',
                'fuente'           => 'landing_page',
                'producto_interes' => 'Mentoría 1:1',
                'presupuesto'      => 800.00,
                'created_at'       => Carbon::now()->subDays(8),
            ],
            [
                'nombre'           => 'Juan Herrera',
                'email'            => 'juan.herrera@example.com',
                'telefono'         => '+573205678901',
                'fuente'           => 'otro',
                'producto_interes' => 'Curso de Marketing Digital',
                'presupuesto'      => 200.00,
                'created_at'       => Carbon::now()->subDays(10),
            ],
            [
                'nombre'           => 'Camila Vargas',
                'email'            => 'camila.vargas@example.com',
                'telefono'         => null,
                'fuente'           => 'instagram',
                'producto_interes' => 'Pack Redes Sociales',
                'presupuesto'      => 350.00,
                'created_at'       => Carbon::now()->subDays(12),
            ],
            [
                'nombre'           => 'Sebastián Mora',
                'email'            => 'sebastian.mora@example.com',
                'telefono'         => '+573306789012',
                'fuente'           => 'referido',
                'producto_interes' => 'Consultoría Empresarial',
                'presupuesto'      => 3000.00,
                'created_at'       => Carbon::now()->subDays(15),
            ],
            [
                'nombre'           => 'Isabella Ruiz',
                'email'            => 'isabella.ruiz@example.com',
                'telefono'         => '+573007890123',
                'fuente'           => 'facebook',
                'producto_interes' => 'Curso de Copywriting',
                'presupuesto'      => 600.00,
                'created_at'       => Carbon::now()->subDays(20),
            ],
            [
                'nombre'           => 'Felipe Castro',
                'email'            => 'felipe.castro@example.com',
                'telefono'         => null,
                'fuente'           => 'landing_page',
                'producto_interes' => null,
                'presupuesto'      => null,
                'created_at'       => Carbon::now()->subDays(25),
            ],
        ];

        foreach ($leads as $lead) {
            Lead::create($lead);
        }

        $this->command->info('✓ Se crearon ' . Lead::count() . ' leads de ejemplo.');
    }
}
