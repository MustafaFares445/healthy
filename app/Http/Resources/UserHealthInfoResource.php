<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserHealthInfoResource",
 *     type="object",
 *     title="User Health Info Resource",
 *     description="User health information resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the health info record"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The ID of the user associated with this health info"
 *     ),
 *     @OA\Property(
 *         property="weight",
 *         type="number",
 *         format="float",
 *         description="The weight of the user in kilograms"
 *     ),
 *     @OA\Property(
 *         property="height",
 *         type="number",
 *         format="float",
 *         description="The height of the user in centimeters"
 *     ),
 *     @OA\Property(
 *         property="activity_level",
 *         type="string",
 *         enum={"sedentary", "active", "very_active"},
 *         description="The activity level of the user"
 *     ),
 *     @OA\Property(
 *         property="dietary_restrictions",
 *         type="string",
 *         description="Dietary restrictions of the user"
 *     ),
 *     @OA\Property(
 *         property="goal",
 *         type="string",
 *         enum={"weight_loss", "maintenance", "muscle_gain"},
 *         description="The fitness goal of the user"
 *     ),
 *     @OA\Property(
 *         property="health_notes",
 *         type="string",
 *         description="Additional health notes"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The date when the health info was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The date when the health info was last updated"
 *     )
 * )
 */
class UserHealthInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'weight' => $this->weight,
            'height' => $this->height,
            'activityLevel' => $this->activity_level,
            'dietaryRestrictions' => $this->dietary_restrictions,
            'goal' => $this->goal,
            'healthNotes' => $this->health_notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}