<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SK>
 */
class SKFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipe = $this->faker->randomElement(['LLDIKTI', 'Pengakuan YPT']);
        $kode = $tipe == 'LLDIKTI' ? 'LLD' : 'YPT';
        return [
            'no_sk' => $this->faker->numerify('SK-###/'.$kode),
            'tmt_mulai' => $this->faker->optional()->date(),
            'file_sk' => 'Pemetaan_sk.pdf',
            'keterangan' => 'keterangan',
            'tipe_sk' => $tipe,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
