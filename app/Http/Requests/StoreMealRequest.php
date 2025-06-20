<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreMealRequest",
 *     title="Store Meal Request",
 *     description="Request body for storing a new meal",
 *     required={"ownerId", "title", "price"},
 *     @OA\Property(
 *         property="ownerId",
 *         type="integer",
 *         description="ID of the owner",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         maxLength=150,
 *         description="Title of the meal",
 *         example="Delicious Pasta"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the meal",
 *         example="A classic Italian pasta dish"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         minimum=0,
 *         description="Price of the meal",
 *         example=12.99
 *     ),
 *     @OA\Property(
 *         property="isAvailable",
 *         type="boolean",
 *         description="Indicates if the meal is available",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="availableFrom",
 *         type="string",
 *         format="time",
 *         description="Time from which the meal is available",
 *         example="12:00:00"
 *     ),
 *     @OA\Property(
 *         property="availableTo",
 *         type="string",
 *         format="time",
 *         description="Time until which the meal is available",
 *         example="18:00:00"
 *     ),
 *     @OA\Property(
 *         property="dietType",
 *         type="string",
 *         enum={"keto", "low_carb", "vegetarian", "vegan", "paleo", "balanced"},
 *         description="Type of diet the meal is suitable for",
 *         example="vegetarian"
 *     ),
 *     @OA\Property(
 *         property="allergenIds",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="List of allergen IDs",
 *         example={1, 2}
 *     ),
 *     @OA\Property(
 *         property="ingredients",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="number", format="float", example=100),
 *             @OA\Property(property="unit", type="string", enum={"tbsp", "g", "piece", "l" , "ml"}, example="g")
 *         ),
 *         description="List of ingredients with their quantities and units",
 *         example={{"id": 1, "quantity": 100, "unit": "g"}}
 *     )
 * )
 */
class StoreMealRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'ownerId' => 'required|exists:users,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
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
        ];
    }

    public function messages()
    {
        return [
            'price.min' => 'Price must be a positive number',
            'available_from.date_format' => 'Available from must be in HH:MM:SS format',
            'available_to.date_format' => 'Available to must be in HH:MM:SS format',
            'ingredients.*.unit.in' => 'Unit must be one of: tbsp, g, piece, l',
        ];
    }
}