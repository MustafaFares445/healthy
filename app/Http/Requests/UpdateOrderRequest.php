<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateOrderRequest",
 *     type="object",
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "confirmed", "preparing", "delivered", "cancelled"},
 *         description="The status of the order"
 *     ),
 *     @OA\Property(
 *         property="deliveryAddress",
 *         type="string",
 *         maxLength=255,
 *         description="The delivery address for the order"
 *     ),
 *     @OA\Property(
 *         property="deliveryTimeSlot",
 *         type="string",
 *         maxLength=50,
 *         description="The delivery time slot for the order"
 *     )
 * )
 */
class UpdateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => [
                'sometimes',
                Rule::in(['pending', 'confirmed', 'preparing', 'delivered', 'cancelled'])
            ],
            'deliveryAddress' => 'sometimes|string|max:255',
            'deliveryTimeSlot' => 'sometimes|string|max:50',
        ];
    }
}