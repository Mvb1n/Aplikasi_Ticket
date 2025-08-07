<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil RoleSeeder terlebih dahulu untuk memastikan peran sudah ada
        $this->call(RoleSeeder::class);

        // Ambil ID dari setiap role SETELAH dibuat
        $adminRole = Role::where('name', 'admin')->first();
        $securityRole = Role::where('name', 'security')->first();
        $staffRole = Role::where('name', 'staff')->first();

        // 1. Membuat User Admin dan menetapkan perannya
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->roles()->attach($adminRole->id);

        // 2. Membuat User Security dan menetapkan perannya
        $securityUser = User::factory()->create([
            'name' => 'Security User',
            'email' => 'security@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $securityUser->roles()->attach($securityRole->id);

        // 2. Membuat User Security dan menetapkan perannya
        $staffUser = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $staffUser->roles()->attach($staffRole->id);

        // 4. Membuat 8 user staff lainnya dan menetapkan perannya
        User::factory(7)->create()->each(function ($user) use ($staffRole) {
            $user->roles()->attach($staffRole->id);
        });

        // 5. Panggil seeder lain
        $this->call([
            RoleSeeder::class,
            SiteSeeder::class,
            AssetSeeder::class,
            IncidentSeeder::class,
            ProblemSeeder::class,
        ]);
    }
}