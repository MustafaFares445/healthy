<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateIngredientRequest",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         maxLength=100,
 *         description="The name of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="calories",
 *         type="integer",
 *         minimum=0,
 *         description="The calorie count of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="protein",
 *         type="number",
 *         minimum=0,
 *         description="The protein content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="carbohydrates",
 *         type="number",
 *         minimum=0,
 *         description="The carbohydrate content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="sugar",
 *         type="number",
 *         minimum=0,
 *         description="The sugar content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="fat",
 *         type="number",
 *         minimum=0,
 *         description="The fat content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="fiber",
 *         type="number",
 *         minimum=0,
 *         description="The fiber content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="sodium",
 *         type="number",
 *         minimum=0,
 *         description="The sodium content of the ingredient"
 *     )
 * )
 */
class UpdateIngredientRequest extends FormRequest
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
                'max:100',
                Rule::unique('ingredients')->ignore($this->ingredient)
            ],
            'calories' => 'sometimes|integer|min:0',
            'protein' => 'sometimes|numeric|min:0',
            'carbohydrates' => 'sometimes|numeric|min:0',
            'sugar' => 'sometimes|numeric|min:0',
            'fat' => 'sometimes|numeric|min:0',
            'fiber' => 'sometimes|numeric|min:0',
            'sodium' => 'sometimes|numeric|min:0',
        ];
    }
}