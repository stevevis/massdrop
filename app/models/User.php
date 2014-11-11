<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

  use UserTrait, RemindableTrait;

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'users';

  /**
   * The database colomn used as the primary key.
   *
   * @var string
   */
  protected $primaryKey = 'user_id';

  /**
  * Disable Eloquent timestamps, we have MySQL triggers to take care of it.
  *
  * @var bool
  */
  public $timestamps = false;

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = array('password', 'remember_token');

  public function Sets()
  {
    return $this->hasMany('Set', 'set_id', 'user_id');
  }

  public function Items()
  {
    return $this->hasMany('Set', 'item_id', 'user_id');
  }

}
