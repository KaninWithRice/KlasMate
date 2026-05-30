<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@rebyu.com',
            'password' => bcrypt('password'),
            'is_superadmin' => true,
        ]);

        User::factory()->create([
            'name' => 'Alexa Smith',
            'email' => 'alexa@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Morco Polo',
            'email' => 'morco@example.com',
            'password' => bcrypt('password'),
        ]);

        $folders = [
            ['name' => 'Mathematics', 'color' => 'bg-[#f5c32f]'],
            ['name' => 'Computer Science', 'color' => 'bg-[#072ac6]'],
            ['name' => 'Physics', 'color' => 'bg-[#07a954]'],
            ['name' => 'History', 'color' => 'bg-[#f50220]']
        ];
        foreach ($folders as $data) {
            \App\Models\Folder::create([
                'name' => $data['name'],
                'color' => $data['color'],
                'user_id' => 1
            ]);
        }
    }
}
