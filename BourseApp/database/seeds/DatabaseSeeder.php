<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        factory(App\User::class)->create();
        factory(App\Share::class, 10)->create();
        factory(App\Order::class, 10)->create();
        factory(App\PriceShares::class, 10)->create();
    }
}
