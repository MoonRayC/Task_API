<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    private static int $taskNumber = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => function () {
                return ProjectFactory::new()->create()->id;
            },
            'title' => 'Task ' . self::$taskNumber++,
            'description' => $this->faker->optional()->paragraph(),
            'priority_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['active', 'on_hold', 'completed']),
            'start_time' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
            'end_time' => $this->faker->optional()->dateTimeBetween('now', '+7 days'),
            'created_at' => $this->faker->dateTimeBetween('-5 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
