<?php

namespace App\JsonApi\V1\Members;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class MemberRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $member = $this->model();
        $memberId = $member ? $member->getKey() : null;

        return [
            'member_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('members', 'member_code')->ignore($memberId),
            ],
            'qr_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('members', 'qr_code')->ignore($memberId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('members', 'email')->ignore($memberId),
            ],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
            'address' => ['nullable', 'string'],
            'voice_part' => [
                'required',
                Rule::in([
                    'soprano',
                    'alto',
                    'tenor',
                    'bass',
                    'contralto',
                    'mezzo_soprano',
                    'baritone',
                    'bass_profundo'
                ]),
            ],
            'join_date' => ['required', 'date', 'before_or_equal:today'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

}
