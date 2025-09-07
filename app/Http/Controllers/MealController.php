<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchMealRequest;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class MealController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/meals",
     *     summary="Get a list of meals",
     *     tags={"Meals"},
     *     @OA\Parameter(
     *         name="available",
     *         in="query",
     *         description="Filter by availability",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="dietType",
     *         in="query",
     *         description="Filter by diet type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="ownerId",
     *         in="query",
     *         description="Filter by owner ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by title",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/MealResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Meal::query()
            ->with(['owner', 'allergens', 'ingredients', 'media'])
            ->inRandomOrder();

        if (!auth()->user()->hasRole('admin')) {
            $query->where('owner_id', Auth::id());
        }

        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        // Filter by diet typeOwner
        if ($request->has('dietType')) {
            $query->where('diet_type', $request->input('dietType'));
        }

        // Filter by owner
        if ($request->has('ownerId')) {
            $query->where('owner_id', $request->input('ownerId'));
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%')
                ->orWhere('description', 'like', '%' . $request->input('search') . '%');
        }

        $meals = $query->latest()
            ->paginate($request->input('perPage', 15));

        return MealResource::collection($meals);
    }

    /**
     * @OA\Post(
     *     path="/api/meals",
     *     summary="Create a new meal",
     *     tags={"Meals"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreMealRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Meal created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MealResource")
     *     )
     * )
     */
    public function store(StoreMealRequest $request): JsonResponse
    {
        $meal = DB::transaction(function () use ($request) {
            $meal = Meal::create([
                'owner_id' => $request->ownerId,
                'title' => $request->title,
                'description' => $request->description,
                'price_cents' => $request->price * 100, // Convert currency to cents
                'is_available' => $request->isAvailable ?? true,
                'available_from' => $request->availableFrom ?? '00:00:00',
                'available_to' => $request->availableTo ?? '23:59:59',
                'diet_type' => $request->dietType,
            ]);

            // Sync allergens
            if ($request->has('allergenIds')) {
                $meal->allergens()->sync($request->allergenIds);
            }

            $this->handleMediaUpload($request , $meal);

            // Sync ingredients with quantities
            if ($request->has('ingredients')) {
                $ingredientsData = collect($request->ingredients)
                    ->mapWithKeys(function ($item) {
                        return [
                            $item['id'] => [
                                'quantity' => $item['quantity'],
                                'unit' => $item['unit']
                            ]
                        ];
                    })
                    ->toArray();

                $meal->ingredients()->sync($ingredientsData);
            }

            return $meal;
        });

        return response()->json([
            'message' => 'Meal created successfully',
            'data' => new MealResource($meal->load(['owner', 'allergens', 'ingredients' , 'media']))
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/meals/{id}",
     *     summary="Get a specific meal",
     *     tags={"Meals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the meal",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/MealResource")
     *     )
     * )
     */
    public function show(Meal $meal): MealResource
    {
        $meal->load(['owner', 'allergens', 'ingredients' , 'reviews.user']);
        return new MealResource($meal);
    }

    /**
     * @OA\Put(
     *     path="/api/meals/{id}",
     *     summary="Update a specific meal",
     *     tags={"Meals"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the meal",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateMealRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Meal updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/MealResource")
     *     )
     * )
     */
    public function update(UpdateMealRequest $request, Meal $meal): JsonResponse
    {
        $meal = DB::transaction(function () use ($request, $meal) {
            $meal->update([
                'owner_id' => $request->ownerId ?? $meal->owner_id,
                'title' => $request->title ?? $meal->title,
                'description' => $request->description ?? $meal->description,
                'price_cents' => $request->has('price') ? $request->price * 100 : $meal->price_cents,
                'is_available' => $request->has('isAvailable') ? $request->isAvailable : $meal->is_available,
                'available_from' => $request->availableFrom ?? $meal->available_from,
                'available_to' => $request->availableTo ?? $meal->available_to, // Fixed: Changed from dietType to availableTo
                'diet_type' => $request->dietType ?? $meal->diet_type,
            ]);

            // Sync allergens if provided
            if ($request->has('allergenIds')) {
                $meal->allergens()->sync($request->allergen_ids);
            }

            if ($request->has('images')) {
                $meal->media()->delete();
                $this->handleMediaUpload($request , $meal);
            }

            // Sync ingredients if provided
            if ($request->has('ingredients')) {
                $ingredientsData = collect($request->ingredients)
                    ->mapWithKeys(function ($item) {
                        return [
                            $item['id'] => [
                                'quantity' => $item['quantity'],
                                'unit' => $item['unit']
                            ]
                        ];
                    })
                    ->toArray();

                $meal->ingredients()->sync($ingredientsData);
            }

            return $meal;
        });

        return response()->json([
            'message' => 'Meal updated successfully',
            'data' => new MealResource($meal->load(['owner', 'allergens', 'ingredients' , 'media']))
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/meals/{id}",
     *     summary="Delete a specific meal",
     *     tags={"Meals"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the meal",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Meal deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Meal deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy(Meal $meal): JsonResponse
    {
        $meal->ingredients()->detach();
        $meal->allergens()->detach();
        $meal->delete();

        return response()->json([
            'message' => 'Meal deleted successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/meals/search",
     *     summary="Search meals with advanced filters",
     *     tags={"Meals"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="query",
     *                 type="string",
     *                 description="Search query for title and description",
     *                 example="healthy breakfast"
     *             ),
     *             @OA\Property(
     *                 property="dietType",
     *                 type="string",
     *                 description="Filter by diet type",
     *                 example="vegetarian"
     *             ),
     *             @OA\Property(
     *                 property="minPrice",
     *                 type="number",
     *                 format="float",
     *                 description="Minimum price filter",
     *                 example=5.00
     *             ),
     *             @OA\Property(
     *                 property="maxPrice",
     *                 type="number",
     *                 format="float",
     *                 description="Maximum price filter",
     *                 example=25.00
     *             ),
     *             @OA\Property(
     *                 property="isAvailable",
     *                 type="boolean",
     *                 description="Filter by availability",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="allergenIds",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Exclude meals with these allergens",
     *                 example={1, 2, 3}
     *             ),
     *             @OA\Property(
     *                 property="ingredientIds",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Include meals with these ingredients",
     *                 example={1, 2, 3}
     *             ),
     *             @OA\Property(
     *                 property="minRating",
     *                 type="number",
     *                 format="float",
     *                 description="Minimum rating filter",
     *                 example=4.0
     *             ),
     *             @OA\Property(
     *                 property="ownerId",
     *                 type="integer",
     *                 description="Filter by owner ID",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="sortBy",
     *                 type="string",
     *                 enum={"title", "price_cents", "rate", "created_at"},
     *                 description="Sort field",
     *                 example="price_cents"
     *             ),
     *             @OA\Property(
     *                 property="sortDirection",
     *                 type="string",
     *                 enum={"asc", "desc"},
     *                 description="Sort direction",
     *                 example="asc"
     *             ),
     *             @OA\Property(
     *                 property="perPage",
     *                 type="integer",
     *                 description="Number of items per page",
     *                 example=15
     *             ),
     *             @OA\Property(
     *                 property="page",
     *                 type="integer",
     *                 description="Page number",
     *                 example=1
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MealResource")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 description="Pagination metadata"
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 description="Pagination links"
     *             )
     *         )
     *     )
     * )
     */
    public function search(SearchMealRequest $request): AnonymousResourceCollection
    {
        $query = Meal::query()
            ->with(['owner', 'allergens', 'ingredients', 'reviews'])
            ->select('meals.*');

        // Text search in title and description
        if ($request->filled('query')) {
            $searchTerm = $request->input('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by diet type
        if ($request->filled('dietType')) {
            $query->where('diet_type', $request->input('dietType'));
        }

        // Price range filter
        if ($request->filled('minPrice')) {
            $query->where('price_cents', '>=', $request->input('minPrice') * 100);
        }
        if ($request->filled('maxPrice')) {
            $query->where('price_cents', '<=', $request->input('maxPrice') * 100);
        }

        // Availability filter
        if ($request->has('isAvailable')) {
            $query->where('is_available', $request->boolean('isAvailable'));
        }

        // Exclude meals with specific allergens
        if ($request->filled('allergenIds')) {
            $query->whereDoesntHave('allergens', function ($q) use ($request) {
                $q->whereIn('allergens.id', $request->input('allergenIds'));
            });
        }

        // Include meals with specific ingredients
        if ($request->filled('ingredientIds')) {
            $query->whereHas('ingredients', function ($q) use ($request) {
                $q->whereIn('ingredients.id', $request->input('ingredientIds'));
            });
        }

        // Minimum rating filter
        if ($request->filled('minRating')) {
            $query->where('rate', '>=', $request->input('minRating'));
        }

        // Filter by owner
        if ($request->filled('ownerId')) {
            $query->where('owner_id', $request->input('ownerId'));
        }

        // Sorting
        $sortBy = $request->input('sortBy', 'created_at');
        $sortDirection = $request->input('sortDirection', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->input('perPage', 15);
        $meals = $query->paginate($perPage);

        return MealResource::collection($meals);
    }

    /**
     * @OA\Get(
     *     path="/api/meals/diet-types",
     *     summary="Get list of diet types",
     *     tags={"Meals"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string")
     *         )
     *     )
     * )
     */
    public function dietTypes(): JsonResponse
    {
        return response()->json([
            'data' => Meal::pluck('diet_type')->unique()->values()->toArray(),
        ]);
    }
}
