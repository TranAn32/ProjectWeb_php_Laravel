<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // admin01 (admin)
        $adminData = [
            'userName' => 'admin01',
            'email' => 'admin01@example.com',
            'password' => Hash::make('Admin@123'),
            'phoneNumber' => '0900000001',
            'address' => 'Hanoi',
            'gender' => 'Male',
            'role' => 'admin',
            'status' => 'Active',
            'updated_at' => $now,
        ];
        $existingAdmin = DB::table('Users')
            ->where('email', $adminData['email'])
            ->orWhere('userName', $adminData['userName'])
            ->first();
        if ($existingAdmin) {
            DB::table('Users')->where('userID', $existingAdmin->userID)->update($adminData);
        } else {
            DB::table('Users')->insert($adminData + ['created_at' => $now]);
        }

        // Khach1 (customer)
        $custData = [
            'userName' => 'Khach1',
            'email' => 'khach1@example.com',
            'password' => Hash::make('Customer@123'),
            'phoneNumber' => '0900000002',
            'address' => 'Ho Chi Minh City',
            'gender' => 'Other',
            'role' => 'customer',
            'status' => 'Active',
            'updated_at' => $now,
        ];
        $existingCust = DB::table('Users')
            ->where('email', $custData['email'])
            ->orWhere('userName', $custData['userName'])
            ->first();
        if ($existingCust) {
            DB::table('Users')->where('userID', $existingCust->userID)->update($custData);
        } else {
            DB::table('Users')->insert($custData + ['created_at' => $now]);
        }
    }
}
