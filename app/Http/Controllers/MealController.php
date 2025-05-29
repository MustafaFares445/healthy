<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Http\Resources\MealResource;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
            ->with(['owner', 'allergens', 'ingredients'])
            ->orderBy('created_at', 'desc');

        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        // Filter by diet type
        if ($request->has('dietType')) {
            $query->where('diet_type', $request->input('dietType'));
        }

        // Filter by owner
        if ($request->has('ownerId')) {
            $query->where('owner_id', $request->input('ownerId'));
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        $meals = $query->paginate($request->input('perPage', 15));

        return MealResource::collection($meals);
    }

    /**
     * @OA\Post(
     *     path="/api/meals",
     *     summary="Create a new meal",
     *     tags={"Meals"},
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
                $meal->allergens()->sync($request->allergen_ids);
            }

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
            'data' => new MealResource($meal->load(['owner', 'allergens', 'ingredients']))
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
        $meal->load(['owner', 'allergens', 'ingredients']);
        return new MealResource($meal);
    }

    /**
     * @OA\Put(
     *     path="/api/meals/{id}",
     *     summary="Update a specific meal",
     *     tags={"Meals"},
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
                'title' => $request->title ?? $meal->title,
                'description' => $request->description ?? $meal->description,
                'price_cents' => $request->has('price') ? $request->price * 100 : $meal->price_cents,
                'is_available' => $request->has('isAvailable') ? $request->isAvailable : $meal->is_available,
                'available_from' => $request->availableFrom ?? $meal->available_from,
                'available_to' => $request->dietType ?? $meal->available_to,
                'diet_type' => $request->dietType ?? $meal->diet_type,
            ]);

            // Sync allergens if provided
            if ($request->has('allergenIds')) {
                $meal->allergens()->sync($request->allergen_ids);
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
            'data' => new MealResource($meal->load(['owner', 'allergens', 'ingredients']))
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/meals/{id}",
     *     summary="Delete a specific meal",
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
     *         description="Meal deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Meal deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy(Meal $meal): JsonResponse
    {
        $meal->delete();

        return response()->json([
            'message' => 'Meal deleted successfully'
        ]);
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
            'data' => Meal::dietTypes()
        ]);
    }
}