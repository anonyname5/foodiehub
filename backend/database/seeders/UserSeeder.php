<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'bio' => 'Food enthusiast and restaurant explorer. Love trying new cuisines and sharing my experiences with fellow food lovers!',
            'avatar' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=100&h=100&fit=crop&crop=face',
            'is_public' => true,
            'email_notifications' => true,
        ]);

        // Create additional sample users
        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password123'),
            'bio' => 'Passionate food blogger and restaurant reviewer.',
            'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop&crop=face',
            'is_public' => true,
            'email_notifications' => true,
        ]);

        User::create([
            'name' => 'Mike Chen',
            'email' => 'mike@example.com',
            'password' => Hash::make('password123'),
            'bio' => 'Chef and food critic with 10+ years of experience.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop&crop=face',
            'is_public' => true,
            'email_notifications' => false,
        ]);
    }
}
