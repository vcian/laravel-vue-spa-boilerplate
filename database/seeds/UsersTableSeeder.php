<?php

use App\User;
use Database\TruncateTable;
use Illuminate\Database\Seeder;
use Database\DisableForeignKeys;

/**
 * Class UsersTableSeeder.
 */
class UsersTableSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        $this->truncate('users');

        factory(User::class,1)->create([
            'email'    => 'admin@admin.com',
            'password' =>  bcrypt('1234')
        ]);

        $this->enableForeignKeys();
    }
}
