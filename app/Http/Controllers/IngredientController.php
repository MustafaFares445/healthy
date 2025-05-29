<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Http\Resources\IngredientResource;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class IngredientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ingredients",
     *     summary="Get a list of ingredients",
     *     tags={"Ingredients"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by ingredient name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="minProtein",
     *         in="query",
     *         description="Filter by minimum protein",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="maxSugar",
     *         in="query",
     *         description="Filter by maximum sugar",
     *         required=false,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/IngredientResource")
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Ingredient::query()
            ->withCount('meals')
            ->orderBy('name');

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by minimum protein
        if ($request->has('minProtein')) {
            $query->where('protein', '>=', $request->get('minProtein'));
        }

        // Filter by maximum sugar
        if ($request->has('maxSugar')) {
            $query->where('sugar', '<=', $request->get('maxSugar'));
        }

        $ingredients = $query->paginate($request->input('perPage', 15));

        return IngredientResource::collection($ingredients);
    }

    /**
     * @OA\Post(
     *     path="/api/ingredients",
     *     summary="Create a new ingredient",
     *     tags={"Ingredients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreIngredientRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ingredient created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/IngredientResource")
     *     )
     * )
     */
    public function store(StoreIngredientRequest $request): JsonResponse
    {
        $ingredient = Ingredient::query()->create($request->validated());

        return response()->json([
            'message' => 'Ingredient created successfully',
            'data' => new IngredientResource($ingredient)
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/ingredients/{id}",
     *     summary="Get a specific ingredient",
     *     tags={"Ingredients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ingredient",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/IngredientResource")
     *     )
     * )
     */
    public function show(Ingredient $ingredient): IngredientResource
    {
        $ingredient->loadCount('meals');

        return new IngredientResource($ingredient);
    }

    /**
     * @OA\Put(
     *     path="/api/ingredients/{id}",
     *     summary="Update an existing ingredient",
     *     tags={"Ingredients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ingredient",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateIngredientRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ingredient updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/IngredientResource")
     *     )
     * )
     */
    public function update(UpdateIngredientRequest $request, Ingredient $ingredient): JsonResponse
    {
        $ingredient->update($request->validated());

        return response()->json([
            'message' => 'Ingredient updated successfully',
            'data' => new IngredientResource($ingredient)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/ingredients/{id}",
     *     summary="Delete an ingredient",
     *     tags={"Ingredients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the ingredient",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ingredient deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete ingredient as it is used in one or more meals"
     *     )
     * )
     */
    public function destroy(Ingredient $ingredient): JsonResponse
    {
        // Check if ingredient is used in any meals
        if ($ingredient->meals()->exists()) {
            return response()->json([
                'message' => 'Cannot delete ingredient as it is used in one or more meals'
            ], 422);
        }

        $ingredient->delete();

        return response()->json([
            'message' => 'Ingredient deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/ingredients/stats",
     *     summary="Get ingredient statistics",
     *     tags={"Ingredients"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="totalIngredients", type="integer"),
     *             @OA\Property(property="highestCalorie", type="object"),
     *             @OA\Property(property="averageProtein", type="number"),
     *             @OA\Property(property="lowestSugar", type="object")
     *         )
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'totalIngredients' => Ingredient::count(),
            'highestCalorie' => Ingredient::orderBy('calories', 'desc')->first(['name', 'calories']),
            'averageProtein' => round(Ingredient::avg('protein'), 2),
            'lowestSugar' => Ingredient::orderBy('sugar')->first(['name', 'sugar']),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}