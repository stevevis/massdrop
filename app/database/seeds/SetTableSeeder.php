<?php

# INSERT INTO `sets` (`set_id`, `name`, `creator`, `description`, `image_url`) VALUES (1, 'My Portable Audio Setup', 1, 'This is how I listen to awesome beats when I\'m out.', 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/EJ6QKT_20140530_194403_0F8FDF5FA221AAC02E.png');

class SetTableSeeder extends Seeder {

  public function run()
  {
    DB::table('sets')->delete();

    Set::create(array(
      'set_id' => 1,
      'name' => 'My Portable Audio Setup',
      'creator' => 1,
      'description' => 'This is how I listen to awesome beats when I\'m out.',
      'image_url' => 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/EJ6QKT_20140530_194403_0F8FDF5FA221AAC02E.png',
    ));
  }

}
