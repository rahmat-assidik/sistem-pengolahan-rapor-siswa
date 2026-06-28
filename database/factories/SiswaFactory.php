<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');
        $gender = $this->faker->randomElement(['Laki-laki', 'Perempuan']);
        return [
            'nis' => $this->faker->unique()->numerify('##########'),
            'nama_siswa' => $faker->name($gender == 'Laki-laki' ? 'male' : 'female'),
            'nama_orang_tua' => $faker->name('male'),
            'jenis_kelamin' => $gender,
            'status' => 'Aktif',
            'angkatan' => $this->faker->randomElement([2022, 2023, 2024]),
        ];
    }
}
