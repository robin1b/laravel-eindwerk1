<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

/**
 * @group Items
 *
 * API endpoints for managing items.
 */
class ItemController extends Controller
{
    /**
     * Get all items.
     *
     * Retrieves a paginated list of items. Supports filtering by status, name,
     * creation date and page size.
     *
     * @queryParam status string Filter by status. Allowed values: new, in_progress, done. Example: new
     * @queryParam name string Filter by partial name match. Example: foo
     * @queryParam created_after date Filter items created on or after this date (YYYY-MM-DD). Example: 2025-01-01
     * @queryParam per_page integer Number of items per page. Example: 15
     *
     * @response 200 {
     *  "current_page":1,
     *  "data":[
     *     {
     *       "id":1,
     *       "name":"Item 1",
     *       "description":"Voorbeeld item",
     *       "status":"new",
     *       "created_at":"2025-05-01T12:34:56Z",
     *       "updated_at":"2025-05-01T12:34:56Z"
     *     }
     *  ],
     *  "from":1,
     *  "last_page":1,
     *  "per_page":15,
     *  "to":1,
     *  "total":1
     * }
     */
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('created_after')) {
            $query->whereDate('created_at', '>=', $request->created_after);
        }

        $perPage = $request->input('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    /**
     * Create a new item.
     *
     * @bodyParam name string required The name of the item. Example: New item
     * @bodyParam description string The description of the item. Example: This is an item.
     * @bodyParam status string Must be one of: new, in_progress, done. Example: new
     *
     * @response 201 {
     *  "id": 123,
     *  "name": "New item",
     *  "description": "This is an item.",
     *  "status": "new",
     *  "created_at":"2025-05-05T10:00:00Z",
     *  "updated_at":"2025-05-05T10:00:00Z"
     * }
     *
     * @response 422 {
     *  "message":"The given data was invalid.",
     *  "errors":{
     *    "name":["The name field is required."]
     *  }
     * }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'in:new,in_progress,done'
        ]);

        $item = Item::create($data);

        return response()->json($item, 201);
    }

    /**
     * Get item details.
     *
     * @urlParam id integer required The ID of the item. Example: 1
     *
     * @response 200 {
     *  "id": 1,
     *  "name": "Item 1",
     *  "description": "Item description.",
     *  "status": "done",
     *  "created_at": "2025-05-01T12:34:56Z",
     *  "updated_at": "2025-05-01T12:34:56Z"
     * }
     * @response 404 {
     *  "message":"No query results for model [App\\Models\\Item] 999"
     * }
     */
    public function show($id)
    {
        $item = Item::findOrFail($id);

        return response()->json($item);
    }

    /**
     * Delete an item.
     *
     * @urlParam id integer required The ID of the item. Example: 1
     *
     * @response 204
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return response()->noContent();
    }
}
