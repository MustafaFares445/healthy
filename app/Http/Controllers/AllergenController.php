<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAllergenRequest;
use App\Http\Requests\UpdateAllergenRequest;
use App\Http\Resources\AllergenResource;
use App\Models\Allergen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class AllergenController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/allergens",
     *     summary="Get a list of allergens",
     *     tags={"Allergens"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by allergen name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
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
     *             @OA\Items(ref="#/components/schemas/AllergenResource")
     *         )
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Allergen::query()
            ->withCount('meals')
            ->orderBy('name');

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $allergens = $query->paginate($request->input('per_page', 15));

        return AllergenResource::collection($allergens);
    }

    /**
     * @OA\Post(
     *     path="/api/allergens",
     *     summary="Create a new allergen",
     *     tags={"Allergens"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreAllergenRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Allergen created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AllergenResource")
     *     )
     * )
     */
    public function store(StoreAllergenRequest $request): JsonResponse
    {
        $allergen = Allergen::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Allergen created successfully',
            'data' => new AllergenResource($allergen)
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/allergens/{id}",
     *     summary="Get a specific allergen",
     *     tags={"Allergens"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the allergen",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AllergenResource")
     *     )
     * )
     */
    public function show(Allergen $allergen): AllergenResource
    {
        $allergen->loadCount('meals');
        return new AllergenResource($allergen);
    }

    /**
     * @OA\Put(
     *     path="/api/allergens/{id}",
     *     summary="Update an existing allergen",
     *     tags={"Allergens"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the allergen",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAllergenRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Allergen updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AllergenResource")
     *     )
     * )
     */
    public function update(UpdateAllergenRequest $request, Allergen $allergen): JsonResponse
    {
        $allergen->update([
            'name' => $request->name ?? $allergen->name,
        ]);

        return response()->json([
            'message' => 'Allergen updated successfully',
            'data' => new AllergenResource($allergen)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/allergens/{id}",
     *     summary="Delete an allergen",
     *     tags={"Allergens"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the allergen",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Allergen deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete allergen as it is associated with one or more meals"
     *     )
     * )
     */
    public function destroy(Allergen $allergen): JsonResponse
    {
        // Check if allergen is used in any meals
        if ($allergen->meals()->exists()) {
            return response()->json([
                'message' => 'Cannot delete allergen as it is associated with one or more meals'
            ], 422);
        }

        $allergen->delete();

        return response()->json([
            'message' => 'Allergen deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/allergens/stats",
     *     summary="Get allergen usage statistics",
     *     tags={"Allergens"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="total_allergens",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="most_common_allergen",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="meals_count", type="integer")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $mostCommon = Allergen::withCount('meals')
            ->orderBy('meals_count', 'desc')
            ->first();

        return response()->json([
            'data' => [
                'total_allergens' => Allergen::count(),
                'most_common_allergen' => $mostCommon ? [
                    'id' => $mostCommon->id,
                    'name' => $mostCommon->name,
                    'meals_count' => $mostCommon->meals_count
                ] : null,
            ]
        ]);
    }
}
