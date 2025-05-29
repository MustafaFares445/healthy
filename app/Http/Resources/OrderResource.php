<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     type="object",
 *     title="Order Resource",
 *     description="Order resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the order"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="The user who placed the order",
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="The ID of the user"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="The name of the user"
 *         )
 *     ),
 *     @OA\Property(
 *         property="total",
 *         type="number",
 *         format="float",
 *         description="The total amount of the order in currency"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="The status of the order"
 *     ),
 *     @OA\Property(
 *         property="deliveryAddress",
 *         type="string",
 *         description="The delivery address of the order"
 *     ),
 *     @OA\Property(
 *         property="deliveryTimeSlot",
 *         type="string",
 *         description="The delivery time slot of the order"
 *     ),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="The items in the order",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                 property="mealId",
 *                 type="integer",
 *                 description="The ID of the meal"
 *             ),
 *             @OA\Property(
 *                 property="mealTitle",
 *                 type="string",
 *                 description="The title of the meal"
 *             ),
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 description="The quantity of the meal"
 *             ),
 *             @OA\Property(
 *                 property="unit_price",
 *                 type="number",
 *                 format="float",
 *                 description="The unit price of the meal in currency"
 *             ),
 *             @OA\Property(
 *                 property="subtotal",
 *                 type="number",
 *                 format="float",
 *                 description="The subtotal of the meal in currency"
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="placedAt",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the order was placed"
 *     ),
 *     @OA\Property(
 *         property="updatedAt",
 *         type="string",
 *         format="date",
 *         description="The date when the order was last updated"
 *     )
 * )
 */
class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'total' => $this->total_cents / 100, // Convert cents to currency
            'status' => $this->status,
            'deliveryAddress' => $this->delivery_address,
            'deliveryTimeSlot' => $this->delivery_time_slot,
            'items' => $this->items->map(function ($item) {
                return [
                    'mealId' => $item->meal_id,
                    'mealTitle' => $item->meal->title,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price_cents / 100,
                    'subtotal' => ($item->quantity * $item->unit_price_cents) / 100,
                ];
            }),
            'placedAt' => $this->placed_at,
            'updatedAt' => $this->updated_at?->toDateString(),
        ];
    }
}