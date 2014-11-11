<?php

class ItemController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * e.g. curl -i --user a@aa.com:a1\! localhost:8000/api/v1/item\?user_id=1
	 *
	 * @return Response
	 */
	public function index()
	{
		$validator = Validator::make(
			Input::all(),
			array(
				'user_id' => 'numeric|required_with:first,last',
				'name' => 'min:3',
				'last' => 'numeric',
				'first' => 'numeric'
			)
		);

		if ($validator->fails()) {
		  return Response::json(array('errors' => $validator->messages()), 400);
		}

		$userId = Input::get('user_id');
		$itemName = Input::get('name');
		$first = Input::get('first');
		$last = Input::get('last');

		if ($first && $last) {
			// Return an error if the user specified both a first and a last parameter
			return Response::json(array('errors' => "Cannot have both first and last parameters"), 400);
		}

		$query = Item::select('*');
		if (is_numeric($userId)) {
			$query = $query->creator($userId);

			if (is_numeric($first)) {
				$query->orderBy('created_date', 'asc');
				$query->take($first);
			} else if (is_numeric($last)) {
				$query->orderBy('created_date', 'desc');
				$query->take($last);
			}
		}

		if (is_string($itemName)) {
			$query = $query->nameLike($itemName);
		}

		// TODO Pagination e.g. Item::Take(50)->skip(50)-get();
		$items = $query->get();

		return Response::json(array('items' => $items->toArray()), 200);
	}


	/**
	 * Store a newly created resource in storage.
	 * e.g. curl -i --user a@aa.com:a1\! -H "Content-Type: application/json" -X POST -d '{"name":"Smart 68 Keyboard","image_url":"http://image.com/1"}' localhost:8000/api/v1/item
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make(
			Input::all(),
			array(
				'name' => 'required|min:3|unique:items',
				'image_url' => 'url'
			)
		);

		if ($validator->fails()) {
			return Response::json(array('errors' => $validator->messages()), 400);
		}

		$item = new Item();
		$item->name = Input::get('name');
		$item->image_url = Input::get('image_url');
		$item->creator = Auth::user()->user_id;
		$item->save();

		$response = Response::json(null, 201);
		$response->header('Location', action('ItemController@show', $item->item_id));
		return $response;
	}


	/**
	 * Display the specified resource.
	 * e.g. curl -i --user a@aa.com:a1\! localhost:8000/api/v1/item/4
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$item = Item::find($id);

		if (is_object($item)) {
			return Response::json($item, 200);
		} else {
			return Response::json(null, 404);
		}
	}


	/**
	 * Update the specified resource in storage.
	 * e.g. curl -i --user b@bb.com:b2\@ -H "Content-Type: application/json" -X PUT -d '{"image_url":"http://www.image.com/2"}' localhost:8000/api/v1/item/6
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$validator = Validator::make(
			Input::all(),
			array('image_url' => 'required|url')
		);

		if ($validator->fails()) {
			return Response::json(array('errors' => $validator->messages()), 400);
		}

		$item = Item::find($id);
		if (!is_object($item)) {
			// Return 404 if the item we are updating does not exist
			return Response::json(null, 404);
		} else {
			// Update the item's image field
			$item->image_url = Input::get('image_url');
			$item->save();

			$response = Response::json(null, 200);
			$response->header('Location', action('ItemController@show', $item->item_id));
			return $response;
		}
	}


	/**
	 * Remove the specified resource from storage.
	 * e.g. curl -i --user a@aa.com:a1\! -H "Content-Type: application/json" -X DELETE localhost:8000/api/v1/item/4
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$item = Item::find($id);

		if (!is_object($item)) {
			// Return 404 Not Found if item does not exist
			return Response::json(null, 404);
		} else if ($item->creator != Auth::user()->user_id) {
			// Return 401 Unauthorized if the item was not created by the authenticated user
			return Response::json(null, 401);
		} else {
			// Delete the item if it exists and it was created by the authenticated user
			$item->delete();
			return Response::json(null, 200);
		}
	}

}
