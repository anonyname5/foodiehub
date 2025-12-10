<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::updateOrCreate(
            ['email' => 'admin@foodiehub.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@foodiehub.com',
                'password' => Hash::make('admin123'),
                'bio' => 'System Administrator for FoodieHub platform',
                'is_admin' => true,
                'role' => 'super_admin',
                'is_active' => true,
                'is_public' => false,
                'email_notifications' => true,
                'location' => 'Malaysia',
                'avatar' => null
            ]
        );

        // Create Regular Admin
        User::updateOrCreate(
            ['email' => 'moderator@foodiehub.com'],
            [
                'name' => 'Content Moderator',
                'email' => 'moderator@foodiehub.com',
                'password' => Hash::make('moderator123'),
                'bio' => 'Content moderator for reviews and restaurants',
                'is_admin' => true,
                'role' => 'admin',
                'is_active' => true,
                'is_public' => false,
                'email_notifications' => true,
                'location' => 'Kuala Lumpur, Malaysia',
                'avatar' => null
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('Super Admin: admin@foodiehub.com / admin123');
        $this->command->info('Moderator: moderator@foodiehub.com / moderator123');
    }
}
