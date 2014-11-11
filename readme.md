## Problem Set - Full-Stack Engineer

The goal here will be to design and build a RESTful JSON API in PHP that allows authenticated users to create, modify, and retrieve sets of items over HTTP. Data should be persisted in a MySQL database. You may use any resources or libraries you wish.

## Functionality

The API should allow us to do the following:
* Authenticate a user and make authenticated calls to the API (you should be able to securely
determine who is making the API call)
* Respond only to authenticated API calls (for all endpoints/functionality)
* Items related:
 * Create items (enforce name uniqueness)
 * Delete items (only if the API caller created the item)
 * Update items picture URL’s (any user can edit any item)
 * Retrieve items by unique id or name
 * Retrieve all items that a user has created
 * Retrieve the last n items created by a user
 * Retrieve the first n items created by a user
* Set related:
 * Create sets
 * Delete sets (only if the API caller created the sets)
 * Update sets (only if the API caller created the set)
   * Add/remove items (not allowed to remove items if there is only 1 item)
   * Update set description
   * Update set picture URL
 * Retrieve sets by unique id
 * Retrieve all sets that a user has created
 * Given a list of n item identifiers, retrieve all sets that contain any of the n items
 * Given a list of n item identifiers, retrieve all sets that contain all n items

## Testing

The server can be run with `php artisan serve`. See `curl.txt` for a list of curl commands you can use to test the server.
