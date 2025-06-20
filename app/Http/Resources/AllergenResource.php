<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AllergenResource",
 *     type="object",
 *     title="Allergen Resource",
 *     description="Allergen resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the allergen"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the allergen"
 *     ),
 *     @OA\Property(
 *         property="mealsCount",
 *         type="integer",
 *         description="The count of meals associated with the allergen"
 *     ),
 *     @OA\Property(
 *         property="createdAt",
 *         type="string",
 *         format="date",
 *         description="The date when the allergen was created"
 *     ),
 *     @OA\Property(
 *         property="updatedAt",
 *         type="string",
 *         format="date",
 *         description="The date when the allergen was last updated"
 *     )
 * )
 */
class AllergenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mealsCount' => $this->whenCounted('meals'),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at
        ];
    }
}