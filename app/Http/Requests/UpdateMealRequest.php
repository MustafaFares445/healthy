<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateMealRequest",
 *     type="object",
 *     @OA\Property(
 *         property="ownerId",
 *         type="integer",
 *         description="The ID of the owner",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         maxLength=150,
 *         description="The title of the meal",
 *         example="Delicious Meal"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the meal",
 *         example="A very tasty meal"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="The price of the meal",
 *         example=9.99
 *     ),
 *     @OA\Property(
 *         property="isAvailable",
 *         type="boolean",
 *         description="Whether the meal is available",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="availableFrom",
 *         type="string",
 *         format="time",
 *         description="The time from which the meal is available",
 *         example="08:00:00"
 *     ),
 *     @OA\Property(
 *         property="availableTo",
 *         type="string",
 *         format="time",
 *         description="The time until which the meal is available",
 *         example="18:00:00"
 *     ),
 *     @OA\Property(
 *         property="dietType",
 *         type="string",
 *         enum={"keto", "low_carb", "vegetarian", "vegan", "paleo", "balanced"},
 *         description="The type of diet",
 *         example="vegetarian"
 *     ),
 *     @OA\Property(
 *         property="allergenIds",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="Array of allergen IDs",
 *         example={1, 2}
 *     ),
 *     @OA\Property(
 *         property="ingredients",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="number", format="float", example=2.5),
 *             @OA\Property(property="unit", type="string", enum={"tbsp", "g", "piece", "l"}, example="g")
 *         ),
 *         description="Array of ingredients with their quantities and units",
 *         example={{"id": 1, "quantity": 2.5, "unit": "g"}}
 *     ),
 *     @OA\Property(
 *         property="images",
 *         type="array",
 *         @OA\Items(type="string", format="binary"),
 *         description="Array of image files for the meal",
 *         example={"image1.jpg", "image2.png"}
 *     )
 * )
 */
class UpdateMealRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ownerId' => 'sometimes|exists:users,id',
            'title' => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'isAvailable' => 'sometimes|boolean',
            'availableFrom' => 'sometimes|date_format:H:i:s',
            'availableTo' => 'sometimes|date_format:H:i:s',
            'dietType' => [
                'nullable',
                Rule::in(['keto', 'low_carb', 'vegetarian', 'vegan', 'paleo', 'balanced'])
            ],
            'allergenIds' => 'sometimes|array',
            'allergenIds.*' => 'exists:allergens,id',
            'ingredients' => 'sometimes|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|min:0',
            'ingredients.*.unit' => [
                'required',
                Rule::in(['tbsp', 'g', 'piece', 'l' , 'ml' , 'cup' , 'spoon'])
            ],
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}