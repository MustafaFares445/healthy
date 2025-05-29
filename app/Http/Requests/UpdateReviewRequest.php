<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateReviewRequest",
 *     type="object",
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
class UpdateReviewRequest extends FormRequest
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
            'userId' => 'sometimes|exists:users,id',
            'mealId' => 'sometimes|exists:meals,id',
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ];
    }
}