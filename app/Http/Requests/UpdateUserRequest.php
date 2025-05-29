<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateUserRequest",
 *     title="UpdateUserRequest",
 *     description="Request schema for updating a user",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         maxLength=255,
 *         description="The name of the user"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         description="The email of the user"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         minLength=8,
 *         description="The password of the user"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *         maxLength=255,
 *         nullable=true,
 *         description="The city of the user"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         maxLength=20,
 *         nullable=true,
 *         description="The phone number of the user"
 *     )
 * )
 */
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $this->user->id,
            'password' => 'sometimes|string|min:8',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }
}