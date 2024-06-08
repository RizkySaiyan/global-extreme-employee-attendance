<?php

namespace Database\Factories\Employee;

use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee\EmployeeUser>
 */
class EmployeeUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employeeId' => Employee::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'password'=> Hash::make('global2024'),
            'role' => $this->faker->numberBetween(1, 2),
        ];
    }
}
