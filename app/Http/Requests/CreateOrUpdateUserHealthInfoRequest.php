<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CreateOrUpdateUserHealthInfoRequest",
 *     type="object",
 *     required={"user_id"},
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The ID of the user",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="weight",
 *         type="number",
 *         format="float",
 *         description="The weight of the user in kilograms",
 *         example=70.5
 *     ),
 *     @OA\Property(
 *         property="height",
 *         type="number",
 *         format="float",
 *         description="The height of the user in centimeters",
 *         example=175.5
 *     ),
 *     @OA\Property(
 *         property="activity_level",
 *         type="string",
 *         enum={"sedentary", "active", "very_active"},
 *         description="The activity level of the user",
 *         example="active"
 *     ),
 *     @OA\Property(
 *         property="dietary_restrictions",
 *         type="string",
 *         description="Dietary restrictions of the user",
 *         example="vegetarian"
 *     ),
 *     @OA\Property(
 *         property="goal",
 *         type="string",
 *         enum={"weight_loss", "maintenance", "muscle_gain"},
 *         description="The fitness goal of the user",
 *         example="weight_loss"
 *     ),
 *     @OA\Property(
 *         property="health_notes",
 *         type="string",
 *         description="Additional health notes",
 *         example="No known allergies"
 *     )
 * )
 */
class CreateOrUpdateUserHealthInfoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'userId' => 'required|exists:users,id',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'activityLevel' => 'nullable',
            'dietaryRestrictions' => 'nullable|string|max:255',
            'goal' => 'nullable',
            'healthNotes' => 'nullable|string',
        ];
    }
}
