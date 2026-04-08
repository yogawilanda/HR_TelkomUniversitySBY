<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pilihan jenis kelamin acak
        $gender = $this->faker->randomElement(['Laki-laki', 'Perempuan']);
        $nama   = $this->faker->name($gender === 'Laki-laki' ? 'male' : 'female');

        return [
            'nama_lengkap'   => $nama,
            'jenis_kelamin' => $gender,

            // Wajib unik → masih pakai unique(), ruang kombinasi besar
            'telepon'        => $this->faker->unique()->numerify('08##########'), // 10 digit sesudah 08
            'nik'            => $this->faker->unique()->numerify('################'), // 16 digit

            // Tidak perlu unik → hilangkan unique()
            'alamat'         => $this->faker->address(),
            'tempat_lahir'   => $this->faker->city(),
            'tgl_lahir'      => $this->faker->dateTimeBetween('-40 years', '-18 years')->format('Y-m-d'),
            'tgl_bergabung'  => $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),

            // Email institusi: domain tetap, lokal part unik & panjang
            'email_institusi'=> Str::slug($nama).'.'.$this->faker->unique()->bothify('########').'@telkomuniversity.ac.id',

            // Email pribadi tetap unik (ruang besar)
            'email_pribadi'  => $this->faker->unique()->safeEmail(),

            'email_verified_at' => null,
            'tipe_pegawai'      => $this->faker->randomElement(['TPA','Dosen']),

            // Username unik tanpa pool kecil (hindari randomNumber(3))
            'username'       => Str::slug($nama).'_'.Str::lower(Str::ulid()),

            // Password default
            'password'       => 'password123'   ,
            'is_admin'       => $this->faker->boolean(10),
            'is_new'       => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }
}
