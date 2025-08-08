<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $object = new Admin();
        $object->name = 'Admin';
        $object->email = 'admin@gmail.com';
        $object->password = Hash::make('admin');
        $object->save();
    }
}
