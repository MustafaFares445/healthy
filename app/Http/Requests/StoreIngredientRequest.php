<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreIngredientRequest",
 *     title="Store Ingredient Request",
 *     description="Request body for storing a new ingredient",
 *     required={"name", "calories", "protein", "carbohydrates", "sugar", "fat", "fiber", "sodium"},
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
 *         description="The calorie content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="protein",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="The protein content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="carbohydrates",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="The carbohydrate content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="sugar",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="The sugar content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="fat",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="The fat content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="fiber",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="The fiber content of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="sodium",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="The sodium content of the ingredient"
 *     )
 * )
 */
class StoreIngredientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:ingredients',
            'calories' => 'required|integer|min:0',
            'protein' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'sugar' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'fiber' => 'required|numeric|min:0',
            'sodium' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'An ingredient with this name already exists',
            '*.min' => 'Nutritional values cannot be negative',
        ];
    }
}