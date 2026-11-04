<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'username' => ['filled', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_.-]+$/', Rule::unique(User::class)->ignore($this->user()->id)],
            'email' => ['filled', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id),
                ...($this->user()->google_id || $this->user()->email_verification_method === 'otp'
                    ? [Rule::in([$this->user()->email])] : []),
            ],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
            'email.in' => 'Email terverifikasi tidak dapat diganti melalui formulir profil.',
        ];
    }
}
