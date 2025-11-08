<?php

namespace App\JsonApi\V1\QRCodeDistributions;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class QRCodeDistributionRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'member' => ['required', JsonApiRule::toOne()],
            'distribution_method' => [
                'required',
                Rule::in(['email', 'sms', 'whatsapp', 'print', 'direct_share']),
            ],
            'sent_at' => ['nullable', 'date'],
            'include_instructions' => ['boolean'],
            'include_qr_image' => ['boolean'],
            'include_member_info' => ['boolean'],
            'status' => [
                'nullable',
                Rule::in(['pending', 'sent', 'failed']),
            ],
            'error_message' => ['nullable', 'string'],
        ];
    }

}
