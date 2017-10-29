<?php

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission_crud_post = new Permission(['name' => 'crud-post', 'display_name' => 'Создание/изменение/удаление записи', 'description' => 'Создание/изменение/удаление записи']);
        $permission_crud_post->save();
        $permission_crud_user = new Permission(['name' => 'crud-user', 'display_name' => 'Создание/изменение/удаление пользователя', 'description' => 'Создание/изменение/удаление записи']);
        $permission_crud_user->save();

        $role = new Role(['name' => 'admin', 'display_name' => 'Админ', 'description' => 'User is allowed to manage and edit other users']);
        $role->save();
        $user = new User(['name' => 'admin', 'email' => 'admin@admin.ru', 'password' => Hash::make('admin123')]);
        $user->save();
        $user->attachRole($role);
        $role->attachPermissions([$permission_crud_post, $permission_crud_user]);

        $role = new Role(['name' => 'manager', 'display_name' => 'Менеджер', 'description' => 'User is allowed to manage']);
        $role->save();
        $user = new User(['name' => 'manager', 'email' => 'manager@manager.ru', 'password' => Hash::make('manager123')]);
        $user->save();
        $user->attachRole($role);
        $role->attachPermission($permission_crud_post);
    }
}
