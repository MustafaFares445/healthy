<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateAllergenRequest",
 *     type="object",
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
class UpdateAllergenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('allergens')->ignore($this->allergen)
            ],
        ];
    }
}