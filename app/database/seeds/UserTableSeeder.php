<?php

class UserTableSeeder extends Seeder {

  public function run()
  {
    DB::table('users')->delete();

    User::create(array(
      'user_id' => 1,
      'name' => 'Wade',
      'email' => 'a@aa.com',
      'password' => Hash::make('a1!')
    ));

    User::create(array(
      'user_id' => 2,
      'name' => 'Ryan',
      'email' => 'b@bb.com',
      'password' => Hash::make('b2@')
    ));

    User::create(array(
      'user_id' => 3,
      'name' => 'Mark',
      'email' => 'c@cc.com',
      'password' => Hash::make('c3#')
    ));
  }

}
