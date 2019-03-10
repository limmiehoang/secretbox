<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call('GroupTableSeeder');

        $this->command->info('Group table seeded!');
    }
}

class GroupTableSeeder extends Seeder
{
    public function run() {
        DB::table('groups')->delete();
        \App\Group::create(array('id' => 1, 'name' => 'Fun Group'));
    }
}
