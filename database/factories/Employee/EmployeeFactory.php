<?php

namespace Database\Factories\Employee;

use App\Services\Number\Generator\EmployeeNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'companyOfficeId' => 4,
            'departmentId' => 1,
            'number' => EmployeeNumber::generate(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'photo' => $this->faker->unique()->lexify('picture_????.jpg'),
            'fatherName' => $this->faker->name('male'),
            'fatherEmail' => $this->faker->unique()->safeEmail(),
            'fatherPhone' => $this->faker->phoneNumber(),
            'motherName' => $this->faker->name(),
            'motherPhone' => $this->faker->phoneNumber(),
            'motherEmail' => $this->faker->unique()->safeEmail(),
        ];
    }
}
