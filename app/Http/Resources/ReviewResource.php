<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ReviewResource",
 *     type="object",
 *     title="Review Resource",
 *     description="Review resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the review"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/UserResource",
 *         description="The user who created the review"
 *     ),
 *     @OA\Property(
 *         property="meal",
 *         ref="#/components/schemas/MealResource",
 *         description="The meal being reviewed"
 *     ),
 *     @OA\Property(
 *         property="rating",
 *         type="integer",
 *         description="The rating given by the user"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         description="The comment provided by the user"
 *     )
 * )
 */
class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'meal' => MealResource::make($this->whenLoaded('meal')),
            'rating' => $this->rating,
            'comment' => $this->comment,
        ];
    }
}
