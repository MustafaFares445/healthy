<?php

namespace App\Http\Controllers;

use App\Http\Resources\MealResource;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class HomePageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/home/meals/matched",
     *     summary="Get matched meals for authenticated user",
     *     tags={"Homepage"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/MealResource")
     *             ),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function matchedMeals(Request $request)
    {
        $aiRecommendations = Http::post(config('ai.recommendation_url') . '/recommend', [
            'user_id' => auth()->id(),
        ]);

        // Extract meal IDs from AI response
        $mealIds = collect($aiRecommendations)->pluck('id')->toArray();

        // Get meals from Laravel model in the order of AI recommendations
        $meals = Meal::whereIn('id', $mealIds)
            ->get()
            ->sortBy(function ($meal) use ($mealIds) {
                return array_search($meal->id, $mealIds);
            });

        return MealResource::collection($meals);
    }

    /**
     * @OA\Get(
     *     path="/api/home/meals/types",
     *     summary="Get meals filtered by diet type",
     *     tags={"Homepage"},
     *     @OA\Parameter(
     *         name="dietType",
     *         in="query",
     *         description="Diet type to filter meals",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"vegetarian", "vegan", "keto", "paleo", "gluten-free", "dairy-free"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/MealResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid diet type"
     *     )
     * )
     */
    public function dietTypesMeals(Request $request)
    {
        $dietType = $request->dietType;
        $meals = Meal::with(['media', 'owner'])
            ->where('diet_type', $dietType)
            ->inRandomOrder()
            ->get();

        return MealResource::collection($meals);
    }
}
