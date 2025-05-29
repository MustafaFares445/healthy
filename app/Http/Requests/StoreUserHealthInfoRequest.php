<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreUserHealthInfoRequest",
 *     type="object",
 *     required={"userId"},
 *     @OA\Property(
 *         property="userId",
 *         type="integer",
 *         description="The ID of the user",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="height",
 *         type="number",
 *         format="float",
 *         description="The height of the user in centimeters",
 *         example=175.5
 *     ),
 *     @OA\Property(
 *         property="weight",
 *         type="number",
 *         format="float",
 *         description="The weight of the user in kilograms",
 *         example=70.5
 *     ),
 *     @OA\Property(
 *         property="bloodPressure",
 *         type="string",
 *         description="The blood pressure of the user",
 *         example="120/80"
 *     ),
 *     @OA\Property(
 *         property="heartRate",
 *         type="integer",
 *         description="The heart rate of the user in beats per minute",
 *         example=72
 *     )
 * )
 */
class StoreUserHealthInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userId' => 'required|exists:users,id',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'bloodPressure' => 'nullable|string',
            'heartRate' => 'nullable|integer',
        ];
    }
}