<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreAllergenRequest",
 *     title="StoreAllergenRequest",
 *     description="Request schema for storing a new allergen",
 *     required={"name"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         maxLength=50,
 *         description="The name of the allergen",
 *         example="Peanuts"
 *     )
 * )
 */
class StoreAllergenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:50|unique:allergens',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'An allergen with this name already exists',
        ];
    }
}