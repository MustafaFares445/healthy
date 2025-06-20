<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="Allergen Resource",
 *     description="Allergen resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *     ),
 *     @OA\Property(
 *         property="createdAt",
 *         type="string",
 *         format="date",
 *     )
 * )
 */
class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'city' => $this->city,
            'phone' => $this->phone,
            'createdAt' => $this->created_at?->toDateString(),
            'roles' => $this->getRoleNames()
        ];
    }
}
