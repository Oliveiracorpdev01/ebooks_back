<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UsersRole extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users_roles';

    protected $fillable = ['user_id', 'role_id'];


    public static function innerjoinUsersPermissions($id)
    {
        $UsersRules = UsersRole::where('users_roles.user_id', $id)
            ->join('roles', 'roles.id', '=', 'users_roles.role_id')
            ->get();

        $Permissions = array();

        foreach ($UsersRules as $input => $roles) {

            $UsersPermissions = RolesPermission::where('roles_permissions.role_id', $roles->id)
                ->join('permissions', 'permissions.id', '=', 'roles_permissions.permission_id')
                ->get();
            $roles->permissions = $UsersPermissions;
            array_push($Permissions, $roles);
        }

        return $Permissions;
    }
}
