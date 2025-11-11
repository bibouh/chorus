<?php

namespace App\JsonApi\V1\Events;

use App\Models\Event;
use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

/**
 * @property Event $resource
 */
class EventResource extends JsonApiResource
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
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'event_type_id' => $this->resource->event_type_id,
            'created_by' => $this->resource->created_by,
            'parent_event_id' => $this->resource->parent_event_id,
            'date' => $this->resource->date ? \Carbon\Carbon::parse($this->resource->date)->format('Y-m-d') : null,
            'time' => $this->resource->time ? \Carbon\Carbon::parse($this->resource->time)->format('H:i:s') : null,
            'is_recurring' => $this->resource->is_recurring,
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
            $this->relation('eventType'),
            $this->relation('creator'),
            $this->relation('parentEvent'),
            $this->relation('childEvents'),
            $this->relation('attendances'),
            $this->relation('recurringEventSchedules'),
        ];
    }

}
