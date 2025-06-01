<?php

namespace Database\Seeders;

use App\Models\Applicant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Applicant::create([
            'name' => 'John Doe',
            'email' => 'applicant@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
