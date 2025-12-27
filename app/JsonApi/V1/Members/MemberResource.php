<?php

namespace App\JsonApi\V1\Members;

use App\Models\Member;
use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

/**
 * @property Member $resource
 */
class MemberResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        return [
            'member_code' => $this->resource->member_code,
            'qr_code' => $this->resource->qr_code,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'address' => $this->resource->address,
            'voice_part' => $this->resource->voice_part,
            'join_date' => $this->resource->join_date,
            'is_active' => $this->resource->is_active,
            'notes' => $this->resource->notes,
            'qr_code_image_url' => $this->resource->qr_code_image_url,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('attendances'),
            $this->relation('qrCodeDistributions'),
        ];
    }

}
