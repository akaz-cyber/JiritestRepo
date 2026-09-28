<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $data=array(
            array(
                'name'=>'Super Admin',
                'email'=>'superadmin@gmail.com',
                'password'=>Hash::make('SU4Dmin!25'),
                'role'=>'super_admin',
                'status'=>'active'
            ),
            array(
                'name'=>'Admin',
                'email'=>'admin@gmail.com',
                'password'=>Hash::make('AdminJiriF4RM!'),
                'role'=>'admin',
                'status'=>'active'
            ),
        );

        DB::table('users')->insert($data);
    }
}
