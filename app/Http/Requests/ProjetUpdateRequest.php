<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjetUpdateRequest extends FormRequest
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
        $projet = $this->route('projet');

        return [
            'nom' => ['required','string','max:255'],
            'description'=> ['nullable','string'],
            'status' => ['required', Rule::in(['active','completed'])],
            'visibility' => ['required', Rule::in(['private','shared','public'])],
            'share_token' => [
                'nullable',
                'uuid',
                Rule::unique('projet','share_token')->ignore($projet->id_projet, 'id_projet'),
            ],
        ];
    }
}
