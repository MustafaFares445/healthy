<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreUserRequest",
 *     title="Store User Request",
 *     description="Request body data for creating a new user",
 *     required={"name", "email", "password"},
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
 *         description="The email address of the user"
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
 *         description="The city of the user"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         maxLength=20,
 *         description="The phone number of the user"
 *     )
 * )
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Change this to `true` if you want to allow all users to make this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ];
    }
}