<?php

class Item extends Eloquent {
  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'items';

  /**
   * The database colomn used as the primary key.
   *
   * @var string
   */
  protected $primaryKey = 'item_id';

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
  protected $hidden = array('pivot');

  public function sets()
  {
    return $this->belongsToMany('Set', 'set_items', 'item_id', 'set_id');
  }

  public function scopeCreator($query, $userId)
  {
    return $query->whereCreator($userId);
  }

  public function scopeNameLike($query, $name)
  {
    return $query->where('name', 'like', "%$name%");
  }
}
