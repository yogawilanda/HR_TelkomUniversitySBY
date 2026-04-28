<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The nama_lengkap and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating Admin User...');

        // Check if admin already exists
        $existingAdmin = User::where('is_admin', true)->first();
        if ($existingAdmin) {
            $this->error('An admin user already exists: ' . $existingAdmin->email);
            return 1;
        }

        // Get user input
        $nama_lengkap = $this->ask('Enter admin nama_lengkap', 'Admin');
        $email_institusi = $this->ask('Enter admin email institusi', 'admin@telkomuniversity.ac.id');

        // Validate email
        if (!filter_var($email_institusi, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return 1;
        }

        // Check if email already exists
        if (User::where('email_institusi', $email_institusi)->exists()) {
            $this->error('A user with this email already exists!');
            return 1;
        }

        $password = $this->secret('Enter admin password (min 8 characters)');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters long!');
            return 1;
        }

        $confirmPassword = $this->secret('Confirm password');

        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match!');
            return 1;
        }

        // Create admin user
        try {
            $admin = User::create([
                'nama_lengkap' => $nama_lengkap,
                'email_institusi' => $email_institusi,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);

            $this->info('✅ Admin user created successfully!');
            $this->table(
                ['ID', 'Nama Lengkap', 'Email', 'Is Admin', 'Created At'],
                [[$admin->id, $admin->nama_lengkap, $admin->email_institusi, $admin->is_admin ? 'Yes' : 'No', $admin->created_at]]
            );

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }
    }
}
