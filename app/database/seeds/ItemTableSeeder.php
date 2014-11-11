<?php

# INSERT INTO `items` (`item_id`, `name`, `creator`, `image_url`) VALUES (1, 'ATH-M50x Professional Monitor Headphones', 1, 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/VDWZ23_20140202_100823_2E9CC3160EB4DE7586.png');
# INSERT INTO `items` (`item_id`, `name`, `creator`, `image_url`) VALUES (2, 'FiiO E17 USB DAC Headphone Amplifier', 1, 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/2FNXFT_20130816_132147_OPWOOEWLU125PK8HXM.png');
# INSERT INTO `items` (`item_id`, `name`, `creator`, `image_url`) VALUES (3, 'iBasso DX50 Digital Audio Player', 1, 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/EJ6QKT_20140530_194403_0F8FDF5FA221AAC02E.png');

class ItemTableSeeder extends Seeder {

  public function run()
  {
    DB::table('items')->delete();

    $set = Set::find(1);

    $item1 = Item::create(array(
      'item_id' => 1,
      'name' => 'ATH-M50x Professional Monitor Headphones',
      'creator' => 1,
      'image_url' => 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/VDWZ23_20140202_100823_2E9CC3160EB4DE7586.png',
    ));
    $item1->sets()->attach(1);

    sleep(1);

    $item2 = Item::create(array(
      'item_id' => 2,
      'name' => 'FiiO E17 USB DAC Headphone Amplifier',
      'creator' => 1,
      'image_url' => 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/2FNXFT_20130816_132147_OPWOOEWLU125PK8HXM.png',
    ));
    $item2->sets()->attach(1);

    sleep(1);

    $item3 = Item::create(array(
      'item_id' => 3,
      'name' => 'iBasso DX50 Digital Audio Player',
      'creator' => 1,
      'image_url' => 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/EJ6QKT_20140530_194403_0F8FDF5FA221AAC02E.png',
    ));
    $item3->sets()->attach(1);

  }

}
