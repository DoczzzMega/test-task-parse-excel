<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create';

    protected $description = 'Создание тестового пользователя';

    private $email = 'admin@mail.com';

    private $password = 'password';


    public function handle()
    {
        $this->warn('Создание пользователя...');

        $this->createUser();

        $this->info('Пользователь создан.');

        $this->info("Логин: {$this->email}");
        $this->info("Пароль: {$this->password}");
    }

    public function createUser()
    {
        User::query()->firstOrCreate([
            'email' => $this->email,
        ], [
            'name' => 'admin',
            'password' => $this->password,
        ]);
    }
}
