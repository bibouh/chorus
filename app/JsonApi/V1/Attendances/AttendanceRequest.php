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
            'event' => JsonApiRule::toOne(),
            'eventId' => ['required', 'exists:events,id'],
            'member' => JsonApiRule::toOne(),
            'memberId' => ['required', 'exists:members,id'],
            'status' => [
                'required',
                Rule::in(['present', 'late', 'absent']),
            ],
            'arrivalTime' => [
                'nullable',
                'date_format:H:i:s',
                'required_if:status,present,late',
            ],
            'scannedAt' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

}
