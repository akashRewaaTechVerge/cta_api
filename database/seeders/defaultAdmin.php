<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;


class defaultAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where("email", "admin@admin.com")->first();
        $developer = User::where("email", "ingeniousmindslab@gmail.com")->first();
        if (!$user) {
            $user = new User();
            $user->uuid = Str::uuid()->toString();
            $user->email  = "admin@admin.com";
            $user->first_name = "Admin";
            $user->last_name = "CTA";
            $user->status = 1;
            $user->password = bcrypt('123456789');
            $user->phone = 8541256325;
            $user->country_code = 91;
            $user->save();
        }

        if (!$developer) {
            $developer = new User();
            $developer->uuid = Str::uuid()->toString();
            $developer->email  = "hr@rewaatechvergegmail.com";
            $developer->first_name = "Devloper";
            $developer->last_name = "CTA";
            $developer->status = 1;
            $developer->password = bcrypt('iml@123456');
            $developer->phone = 8547859652;
            $developer->country_code = 91;
            $developer->save();
        }

        
        $role = Role::create(['name' => 'Admin']);
        $role1 = Role::create(['name' => 'Developer']);
        $role2 = Role::create(['name' => 'User']);

        $user->assignRole([$role->id]);
        $developer->assignRole([$role1->id]);

    }   
}