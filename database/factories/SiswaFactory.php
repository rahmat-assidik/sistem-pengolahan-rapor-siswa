<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['Laki-laki', 'Perempuan']);
        return [
            'nis' => $this->faker->unique()->numerify('##########'),
            'nama_siswa' => $this->faker->name($gender == 'Laki-laki' ? 'male' : 'female'),
            'jenis_kelamin' => $gender,
            'status' => 'Aktif',
            'angkatan' => $this->faker->randomElement([2022, 2023, 2024]),
        ];
    }
}
