<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreReviewRequest",
 *     type="object",
 *     required={"userId", "mealId", "rating"},
 *     @OA\Property(
 *         property="userId",
 *         type="integer",
 *         description="The ID of the user submitting the review",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="mealId",
 *         type="integer",
 *         description="The ID of the meal being reviewed",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="rating",
 *         type="integer",
 *         description="The rating given to the meal (1-5)",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         description="An optional comment about the meal",
 *         example="This meal was delicious!"
 *     )
 * )
 */
class StoreReviewRequest extends FormRequest
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
            'userId' => 'required|exists:users,id',
            'mealId' => 'required|exists:meals,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ];
    }
}
