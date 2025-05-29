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
 *         property="userId",
 *         type="integer",
 *         description="The ID of the user associated with this health info"
 *     ),
 *     @OA\Property(
 *         property="height",
 *         type="number",
 *         format="float",
 *         description="The height of the user"
 *     ),
 *     @OA\Property(
 *         property="weight",
 *         type="number",
 *         format="float",
 *         description="The weight of the user"
 *     ),
 *     @OA\Property(
 *         property="bloodPressure",
 *         type="string",
 *         description="The blood pressure of the user"
 *     ),
 *     @OA\Property(
 *         property="heartRate",
 *         type="integer",
 *         description="The heart rate of the user"
 *     ),
 *     @OA\Property(
 *         property="createdAt",
 *         type="string",
 *         format="date",
 *         description="The date when the health info was created"
 *     ),
 *     @OA\Property(
 *         property="updatedAt",
 *         type="string",
 *         format="date",
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
            'height' => $this->height,
            'weight' => $this->weight,
            'bloodPressure' => $this->blood_pressure,
            'heartRate' => $this->heart_rate,
            'createdAt' => $this->created_at?->toDateString(),
            'updatedAt' => $this->updated_at?->toDateString(),
        ];
    }
}