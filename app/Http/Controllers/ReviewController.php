<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reviews",
     *     summary="Get all reviews",
     *     tags={"Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of reviews",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ReviewResource")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return ReviewResource::collection(
            Review::with(['user', 'meal.owner'])->paginate(request()->get('perPage' , 15))
        );
    }

    /**
     * @OA\Post(
     *     path="/api/reviews",
     *     summary="Create a new review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreReviewRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Review created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ReviewResource")
     *     )
     * )
     */
    public function store(StoreReviewRequest $request)
    {
        $review = Review::query()->create([
            'user_id' => $request->input('userId'),
            'meal_id' => $request->input('mealId'),
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return ReviewResource::make($review->load('meal'));
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/{id}",
     *     summary="Get a specific review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the review to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review details",
     *         @OA\JsonContent(ref="#/components/schemas/ReviewResource")
     *     )
     * )
     */
    public function show(Review $review)
    {
        return ReviewResource::make($review->load(['user' , 'meal.owner']));
    }

    /**
     * @OA\Put(
     *     path="/api/reviews/{id}",
     *     summary="Update a review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the review to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateReviewRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ReviewResource")
     *     )
     * )
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $review->update([
            'user_id' => $request->input('userId') ?? $review->user_id,
            'meal_id' => $request->input('mealId') ?? $review->meal_id,
            'rating' => $request->input('rating') ?? $review->rating,
            'comment' => $request->input('comment') ?? $review->comment,
        ]);

       return ReviewResource::make($review->load('meal'));
    }

    /**
     * @OA\Delete(
     *     path="/api/reviews/{id}",
     *     summary="Delete a review",
     *     tags={"Reviews"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the review to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Review deleted successfully"
     *     )
     * )
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json(null, 204);
    }
}
