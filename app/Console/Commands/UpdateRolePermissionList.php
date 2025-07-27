<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Models\Permission;

class UpdateRolePermissionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'panel:update-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fresh roles from config file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permissionsList = collect(config('admin-template.permissions-list'));
        $permissionsList->map(function ($value) {
            try {
                Permission::create(['name' => $value]);
            } catch (PermissionAlreadyExists $e) {

            }
        });
        $this->info('permission update was successfully');
        return Command::SUCCESS;
    }
}
