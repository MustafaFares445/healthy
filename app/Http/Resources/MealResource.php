<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MealResource",
 *     type="object",
 *     title="Meal Resource",
 *     description="Meal resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the meal"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the meal"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the meal"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the meal in currency"
 *     ),
 *     @OA\Property(
 *         property="isAvailable",
 *         type="boolean",
 *         description="Indicates if the meal is available"
 *     ),
 *     @OA\Property(
 *         property="availability",
 *         type="object",
 *         description="The availability time frame of the meal",
 *         @OA\Property(
 *             property="from",
 *             type="string",
 *             format="date-time",
 *             description="The start time of availability"
 *         ),
 *         @OA\Property(
 *             property="to",
 *             type="string",
 *             format="date-time",
 *             description="The end time of availability"
 *         )
 *     ),
 *     @OA\Property(
 *         property="dietType",
 *         type="string",
 *         description="The diet type of the meal"
 *     ),
 *     @OA\Property(
 *         property="owner",
 *         ref="#/components/schemas/UserResource",
 *         description="The owner of the meal"
 *     ),
 *     @OA\Property(
 *         property="allergens",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AllergenResource"),
 *         description="List of allergens associated with the meal"
 *     ),
 *     @OA\Property(
 *         property="ingredients",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/IngredientResource"),
 *         description="List of ingredients in the meal"
 *     ),
 *     @OA\Property(
 *          property="images",
 *          type="array",
 *          @OA\Items(ref="#/components/schemas/MediaResource"),
 *          description="List of ingredients in the meal"
 *      ),
 *     @OA\Property(
 *         property="createdAt",
 *         type="string",
 *         format="date",
 *         description="The creation date of the meal"
 *     ),
 *     @OA\Property(
 *         property="updatedAt",
 *         type="string",
 *         format="date",
 *         description="The last update date of the meal"
 *     )
 * )
 */
class MealResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price_cents / 100, // Convert cents to currency
            'isAvailable' => $this->is_available,
            'availability' => [
                'from' => $this->available_from->toTimeString(),
                'to' => $this->available_to->toTimeString(),
            ],
            'dietType' => $this->diet_type,
            'rate' => $this->rate,
            'owner' => UserResource::make($this->whenLoaded('owner')),
            'allergens' => AllergenResource::collection($this->whenLoaded('allergens')),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
            'reviews'   => ReviewResource::collection($this->whenLoaded('reviews')),
            'primaryImage' => MediaResource::make($this->getFirstMedia('images')),
            'images' => MediaResource::collection($this->getMedia('images')),
            'createdAt' => $this->created_at?->toDateString(),
            'updatedAt' => $this->updated_at?->toDateString(),
        ];
    }
}
