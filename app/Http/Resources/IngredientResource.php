<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="IngredientResource",
 *     type="object",
 *     title="Ingredient Resource",
 *     description="Ingredient resource representation",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the ingredient"
 *     ),
 *     @OA\Property(
 *         property="nutritionalInfo",
 *         type="object",
 *         description="Nutritional information of the ingredient",
 *         @OA\Property(
 *             property="calories",
 *             type="number",
 *             format="float",
 *             description="Calories per serving"
 *         ),
 *         @OA\Property(
 *             property="macros",
 *             type="object",
 *             description="Macronutrient information",
 *             @OA\Property(
 *                 property="protein",
 *                 type="number",
 *                 format="float",
 *                 description="Protein content in grams"
 *             ),
 *             @OA\Property(
 *                 property="carbohydrates",
 *                 type="number",
 *                 format="float",
 *                 description="Carbohydrate content in grams"
 *             ),
 *             @OA\Property(
 *                 property="sugar",
 *                 type="number",
 *                 format="float",
 *                 description="Sugar content in grams"
 *             ),
 *             @OA\Property(
 *                 property="fat",
 *                 type="number",
 *                 format="float",
 *                 description="Fat content in grams"
 *             )
 *         ),
 *         @OA\Property(
 *             property="fiber",
 *             type="number",
 *             format="float",
 *             description="Fiber content in grams"
 *         ),
 *         @OA\Property(
 *             property="sodium",
 *             type="number",
 *             format="float",
 *             description="Sodium content in milligrams"
 *         ),
 *         @OA\Property(
 *             property="unit",
 *             type="string",
 *             description="Unit of measurement for the ingredient's nutritional values"
 *         )
 *     ),
 *     @OA\Property(
 *         property="mealsCount",
 *         type="integer",
 *         description="Number of meals associated with this ingredient"
 *     ),
 *     @OA\Property(
 *         property="mealQuantity",
 *         type="number",
 *         format="float",
 *         nullable=true,
 *         description="Quantity of the ingredient in the meal (if loaded via pivot)"
 *     ),
 *     @OA\Property(
 *         property="mealUnit",
 *         type="string",
 *         nullable=true,
 *         description="Unit of measurement for the ingredient in the meal (if loaded via pivot)"
 *     ),
 *     @OA\Property(
 *         property="createdAt",
 *         type="string",
 *         format="date",
 *         description="The date when the ingredient was created"
 *     ),
 *     @OA\Property(
 *         property="updatedAt",
 *         type="string",
 *         format="date",
 *         description="The date when the ingredient was last updated"
 *     )
 * )
 */
class IngredientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nutritionalInfo' => [
                'calories' => $this->calories,
                'macros' => [
                    'protein' => $this->protein,
                    'carbohydrates' => $this->carbohydrates,
                    'sugar' => $this->sugar,
                    'fat' => $this->fat,
                ],
                'fiber' => $this->fiber,
                'sodium' => $this->sodium,
                'unit'  => $this->unit,
            ],
            'mealsCount' => $this->whenCounted('meals'),
            'mealQuantity'  => $this->whenPivotLoaded('ingredient_meal' , $this->pivot?->quantity),
            'mealUnit'  => $this->whenPivotLoaded('ingredient_meal' , $this->pivot?->unit),
            'createdAt' => $this->created_at?->toDateString(),
            'updatedAt' => $this->updated_at?->toDateString(),
        ];
    }
}
