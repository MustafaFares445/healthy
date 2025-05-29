<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreOrderRequest",
 *     type="object",
 *     required={"userId", "deliveryAddress", "deliveryTimeSlot", "items"},
 *     @OA\Property(
 *         property="userId",
 *         type="integer",
 *         description="The ID of the user placing the order",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="deliveryAddress",
 *         type="string",
 *         description="The delivery address for the order",
 *         example="123 Main St, Springfield, IL"
 *     ),
 *     @OA\Property(
 *         property="deliveryTimeSlot",
 *         type="string",
 *         description="The selected delivery time slot",
 *         example="10:00 AM - 12:00 PM"
 *     ),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         description="List of items in the order",
 *         @OA\Items(
 *             type="object",
 *             required={"mealId", "quantity"},
 *             @OA\Property(
 *                 property="meal_id",
 *                 type="integer",
 *                 description="The ID of the meal",
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 description="The quantity of the meal",
 *                 example=2
 *             )
 *         )
 *     )
 * )
 */
class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userId' => 'required|exists:users,id',
            'deliveryAddress' => 'required|string|max:255',
            'deliveryTimeSlot' => 'required|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.mealId' => 'required|exists:meals,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'At least one meal must be ordered',
            'items.*.quantity.min' => 'Quantity must be at least 1',
        ];
    }
}