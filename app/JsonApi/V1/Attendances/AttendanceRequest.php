<?php

namespace App\JsonApi\V1\Attendances;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class AttendanceRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'event' => ['required', JsonApiRule::toOne()],
            'member' => ['required', JsonApiRule::toOne()],
            'status' => [
                'required',
                Rule::in(['present', 'late', 'absent']),
            ],
            'arrival_time' => [
                'nullable',
                'date_format:H:i',
                'required_if:status,present,late',
            ],
            'scanned_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

}
