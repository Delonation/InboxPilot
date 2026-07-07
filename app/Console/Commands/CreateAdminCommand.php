<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Creates an approved admin account. This is the only way to create an admin;
 * public registration can never produce one. Run during installation.
 */
class CreateAdminCommand extends Command
{
    protected $signature = 'inboxpilot:create-admin
                            {--name= : Admin display name}
                            {--email= : Admin email address}
                            {--password= : Admin password (omit for a secure prompt)}';

    protected $description = 'Create the first admin account for InboxPilot';

    public function handle(): int
    {
        $name = $this->option('name') ?: text('Admin name', required: true);
        $email = $this->option('email') ?: text('Admin email', required: true);
        $plain = $this->option('password') ?: password('Admin password (min 8 chars)', required: true);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $plain],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plain),
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_APPROVED,
            'email_verified_at' => now(),
            'approved_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $user->id,
            'sender_name' => $user->name,
            'setup_completed_at' => now(),
        ]);

        $this->info("Admin account created: {$email}");

        return self::SUCCESS;
    }
}
