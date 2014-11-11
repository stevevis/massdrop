<?php

class SetController extends \BaseController {

  /**
   * Display a listing of the resource.
   * e.g. curl -i --user a@aa.com:a1\! localhost:8000/api/v1/set\?user_id=1
   * e.g. curl -s --user a@aa.com:a1\! localhost:8000/api/v1/set\?contains_all=1,2 | python -m json.tool
   *
   * @return Response
   */
  public function index()
  {
    $validator = Validator::make(
      Input::all(),
      array(
        'user_id' => 'numeric|exists:users'
      )
    );

    if ($validator->fails()) {
      return Response::json(array('errors' => $validator->messages()), 400);
    }

    $query = Set::select('*')->with('items');

    $containsAny = explode(',', Input::get('contains_any'));
    $containsAll = explode(',', Input::get('contains_all'));

    if (is_array($containsAny) && count(array_filter($containsAny, 'strlen')) > 0) {
      $sets = DB::table('sets')->join('set_items', 'sets.set_id', '=', 'set_items.set_id')
          ->select('sets.set_id')
          ->whereIn('set_items.item_id', $containsAny)
          ->groupBy('sets.set_id')
          ->get();
      if (count($sets) > 0) {
        $setIds = array_map(function($set) { return $set->set_id; }, $sets);
        $query = $query->whereIn('set_id', $setIds);
      } else {
        // No sets contain any of the specified items, return empty
        return Response::json(array('sets' => []), 200);
      }
    } else if (is_array($containsAll) && count(array_filter($containsAll, 'strlen')) > 0) {
      $sets = DB::table('sets')->join('set_items', 'sets.set_id', '=', 'set_items.set_id')
          ->select('sets.set_id')
          ->whereIn('set_items.item_id', $containsAll)
          ->groupBy('sets.set_id')
          ->havingRaw('COUNT(sets.set_id) = ' . count($containsAll))
          ->get();
      if (count($sets) > 0) {
        $setIds = array_map(function($set) { return $set->set_id; }, $sets);
        $query = $query->whereIn('set_id', $setIds);
      } else {
        // No sets contain all the specified items, return empty
        return Response::json(array('sets' => []), 200);
      }
    }

    $userId = Input::get('user_id');
    if (is_numeric($userId)) {
      $query = $query->creator($userId);
    }

    // TODO Pagination e.g. Item::Take(50)->skip(50)-get();
    $sets = $query->get();

    return Response::json(array('sets' => $sets->toArray()), 200);
  }


  /**
   * Store a newly created resource in storage.
   * e.g. curl -i --user a@aa.com:a1\! -H "Content-Type: application/json" -X POST -d '{"name":"My Gaming Setup", "description":"This is my awesome rig.", "image_url":"http://image.com/1", "items":[7]}' localhost:8000/api/v1/set
   *
   * @return Response
   */
  public function store()
  {
    $validator = Validator::make(
      Input::all(),
      array(
        'name' => 'required|min:3',
        'description' => 'required',
        'items' => 'array|min:1'
      )
    );

    if ($validator->fails()) {
      return Response::json(array('errors' => $validator->messages()), 400);
    }

    $set = new Set();
    $set->name = Input::get('name');
    $set->description = Input::get('description');
    $set->image_url = Input::get('image_url');
    $set->creator = Auth::user()->user_id;
    $set->save();

    foreach (Input::get('items') as $itemId) {
      if (Item::find($itemId)) {
        $set->items()->attach($itemId);
      }
    }

    $response = Response::json(null, 201);
    $response->header('Location', action('SetController@show', $set->set_id));
    return $response;
  }


  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    $set = Set::with('items')->find($id);

    if (is_object($set)) {
      return Response::json($set, 200);
    } else {
      return Response::json(null, 404);
    }
  }


  /**
   * Update the specified resource in storage.
   * e.g. curl -i --user a@aa.com:a1\! -H "Content-Type: application/json" -X PUT -d '{"image_url":"http://image.com/2"}' localhost:8000/api/v1/set/2
   * e.g. curl -i --user a@aa.com:a1\! -H "Content-Type: application/json" -X PUT -d '{"items":[1,2]}' localhost:8000/api/v1/set/2
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    $validator = Validator::make(
      Input::all(),
      array(
        'image_url' => 'url',
        'items' => 'array|min:1'
      )
    );

    if ($validator->fails()) {
      return Response::json(array('errors' => $validator->messages()), 400);
    }

    // Validate that all items in the items array exist
    $itemIds = Input::get('items');
    if (is_array($itemIds)) {
      if (count($itemIds) == 0) {
        return Response::json(array('errors' => "Can't have a set with no items"), 400);
      }
      $items = Item::whereIn('item_id', $itemIds)->get();
      if ($items->count() != count($itemIds)) {
        return Response::json(array('errors' => "Items don't all exist"), 400);
      }
    }

    $set = Set::find($id);
    if (!is_object($set)) {
      // Return 404 if the set we are updating does not exist
      return Response::json(null, 404);
    } else if ($set->creator != Auth::user()->user_id) {
    // Return 401 Unauthorized if the set was not created by the authenticated user
      return Response::json(null, 401);
    } else {
      // Update the set's image field
      if (is_string(Input::get('image_url'))) {
        $set->image_url = Input::get('image_url');
      }

      // Update the set's description field
      if (is_string(Input::get('description'))) {
        $set->description = Input::get('description');
      }

      // Replace the array of items in the set
      $itemIds = Input::get('items');
      if (is_array($itemIds)) {
        $set->items()->sync(Input::get('items'));
      }

      $set->save();
      $response = Response::json(null, 200);
      $response->header('Location', action('SetController@show', $set->set_id));
      return $response;
    }
  }


  /**
   * Remove the specified resource from storage.
   * e.g. curl -i --user a@aa.com:a1\! -H "Content-Type: application/json" -X DELETE localhost:8000/api/v1/set/2
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    $set = Set::find($id);

    if (!is_object($set)) {
      // Return 404 Not Found if item does not exist
      return Response::json(null, 404);
    } else if ($set->creator != Auth::user()->user_id) {
      // Return 401 Unauthorized if the item was not created by the authenticated user
      return Response::json(null, 401);
    } else {
      // Delete the set if it exists and it was created by the authenticated user
      $set->delete();
      return Response::json(null, 200);
    }
  }

  /**
   * Add an item to a set.
   * e.g. curl -i --user a@aa.com:a1\! localhost:8000/api/v1/set/1/item/1
   *
   * @param  int $setId
   * @param  int $itemId
   * @return Response
   */
  public function addItem($setId, $itemId)
  {
    $set = Set::find($setId);
    $item = Item::find($itemId);

    if (!is_object($set)) {
      // Return 404 Not Found if the set does not exist
      return Response::json(array('errors' => array("The set does not exist")), 404);
    } else if (!is_object($item)) {
      // Return 404 Not Found if the item does not exist
      return Response::json(array('errors' => array("The item does not exist")), 404);
    } else if ($set->creator != Auth::user()->user_id) {
      // Return 401 Unauthorized if the set was not created by the authenticated user
      return Response::json(null, 401);
    } else if ($set->items->contains($itemId)) {
      // Return 409 Conflict if the set already contains the item
      return Response::json(array('errors' => array("The set already contains that item")), 409);
    } else {
      // Attach the item to the set
      $set->items()->attach($itemId);
    }
  }

  /**
  * Remove an item from a set.
  * e.g. curl -i --user a@aa.com:a1\! -X DELETE localhost:8000/api/v1/set/1/item/1
  *
  * @param  int $setId
  * @param  int $itemId
  * @return Response
  */
  public function removeItem($setId, $itemId)
  {
    $set = Set::find($setId);
    $item = Item::find($itemId);

    if (!is_object($set)) {
      // Return 404 Not Found if the set does not exist
      return Response::json(array('errors' => array("The set does not exist")), 404);
    } else if (!is_object($item)) {
      // Return 404 Not Found if the item does not exist
      return Response::json(array('errors' => array("The item does not exist")), 404);
    } else if ($set->creator != Auth::user()->user_id) {
      // Return 401 Unauthorized if the set was not created by the authenticated user
      return Response::json(null, 401);
    } else if (!$set->items->contains($itemId)) {
      // Return 404 Not Found if the set does not contain the item
      return Response::json(array('errors' => array("The set already contains that item")), 404);
    } else if ($set->items()->count() < 2) {
      // Return 409 Conflict if this is the only item left in the set
      return Response::json(array('errors' => array("You cannot remove the only item in a set")), 409);
    } else {
      // Attach the item to the set
      $set->items()->detach($itemId);
    }
  }

}
