<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'query' => 'nullable|string|max:255',
            'dietType' => 'nullable|string|max:100',
            'minPrice' => 'nullable|numeric|min:0',
            'maxPrice' => 'nullable|numeric|min:0|gte:minPrice',
            'isAvailable' => 'nullable|boolean',
            'allergenIds' => 'nullable|array',
            'allergenIds.*' => 'integer|exists:allergens,id',
            'ingredientIds' => 'nullable|array',
            'ingredientIds.*' => 'integer|exists:ingredients,id',
            'minRating' => 'nullable|numeric|min:0|max:5',
            'ownerId' => 'nullable|integer|exists:users,id',
            'sortBy' => 'nullable|string|in:title,price_cents,rate,created_at',
            'sortDirection' => 'nullable|string|in:asc,desc',
            'perPage' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.max' => 'The search query must not exceed 255 characters.',
            'maxPrice.gte' => 'The maximum price must be greater than or equal to the minimum price.',
            'allergenIds.*.exists' => 'One or more allergen IDs do not exist.',
            'ingredientIds.*.exists' => 'One or more ingredient IDs do not exist.',
            'minRating.max' => 'The minimum rating cannot exceed 5.',
            'ownerId.exists' => 'The specified owner does not exist.',
            'sortBy.in' => 'The sort field must be one of: title, price_cents, rate, created_at.',
            'sortDirection.in' => 'The sort direction must be either asc or desc.',
            'perPage.max' => 'The per page limit cannot exceed 100.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'dietType' => 'diet type',
            'minPrice' => 'minimum price',
            'maxPrice' => 'maximum price',
            'isAvailable' => 'availability',
            'allergenIds' => 'allergen IDs',
            'ingredientIds' => 'ingredient IDs',
            'minRating' => 'minimum rating',
            'ownerId' => 'owner ID',
            'sortBy' => 'sort field',
            'sortDirection' => 'sort direction',
            'perPage' => 'items per page',
        ];
    }
}

