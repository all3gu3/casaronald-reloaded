<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea (o actualiza) la cuenta maestra a partir de las variables de entorno
 * MASTER_NAME / MASTER_EMAIL / MASTER_PASSWORD (vía config/casaronald.php). La cuenta maestra es la única
 * que puede crear y desactivar cuentas del personal.
 */
class MasterUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('casaronald.master.email');
        $password = config('casaronald.master.password');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('casaronald.master.name'),
                'password' => Hash::make($password),
                'role' => Role::Master,
                'is_active' => true,
            ],
        );

        if ($password === 'cambiame-al-instalar') {
            $this->command?->warn('La cuenta maestra usa la contraseña por defecto: define MASTER_PASSWORD en .env y vuelve a sembrar.');
        }
    }
}
