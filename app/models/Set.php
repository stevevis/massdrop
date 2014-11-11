<?php

class Set extends Eloquent {
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'sets';

  /**
   * The database colomn used as the primary key.
   *
   * @var string
   */
  protected $primaryKey = 'set_id';

  /**
  * Disable Eloquent timestamps, we have MySQL triggers to take care of it.
  *
  * @var bool
  */
  public $timestamps = false;

  public function items()
  {
      return $this->belongsToMany('Item', 'set_items', 'set_id', 'item_id');
  }

  public function scopeCreator($query, $userId)
  {
    return $query->whereCreator($userId);
  }
}
