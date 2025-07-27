<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixUserNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix users with null first_name or last_name';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing user names...');

        $users = User::whereNull('first_name')
            ->orWhereNull('last_name')
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users with null names found.');
            return;
        }

        $count = 0;
        foreach ($users as $user) {
            if (empty($user->first_name)) {
                $user->first_name = 'کاربر';
            }
            if (empty($user->last_name)) {
                $user->last_name = 'سیستم';
            }
            $user->save();
            $count++;
        }

        $this->info("Fixed {$count} users.");
    }
} 