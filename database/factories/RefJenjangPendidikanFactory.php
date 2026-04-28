<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RefJenjangPendidikan>
 */
class RefJenjangPendidikanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jenjang_pendidikan' => null,
            'tingkat' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
