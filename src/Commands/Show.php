<?php

namespace Yusronarif\RBAC\Commands;

use Illuminate\Console\Command;
use Yusronarif\RBAC\Models\Role;
use Illuminate\Support\Collection;
use Yusronarif\RBAC\Models\Permission;

class Show extends Command
{
    protected $signature = 'permission:show
            {guard? : The name of the guard}
            {style? : The display style (default|borderless|compact|box)}';

    protected $description = 'Show a table of roles and permissions per guard';

    public function handle()
    {
        $style = $this->argument('style') ?? 'default';
        $guard = $this->argument('guard');

        if ($guard) {
            $guards = Collection::make([$guard]);
        } else {
            $guards = Permission::pluck('guard_name')->merge(Role::pluck('guard_name'))->unique();
        }

        foreach ($guards as $guard) {
            $this->info("Guard: $guard");

            $roles = Role::whereGuardName($guard)->orderBy('name')->get()->mapWithKeys(function (Role $role) {
                return [$role->name => $role->permissions->pluck('name')];
            });

            $permissions = Permission::whereGuardName($guard)->orderBy('name')->pluck('name');

            $body = $permissions->map(function ($permission) use ($roles) {
                return $roles->map(function (Collection $role_permissions) use ($permission) {
                    return $role_permissions->contains($permission) ? ' ✔' : ' ·';
                })->prepend($permission);
            });

            $this->table(
                $roles->keys()->prepend('')->toArray(),
                $body->toArray(),
                $style
            );
        }
    }
}
