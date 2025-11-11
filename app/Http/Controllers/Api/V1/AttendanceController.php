<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventTypeLateThreshold;
use App\Models\LateDetectionSetting;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

class AttendanceController extends Controller
{

    use Actions\FetchMany;
    use Actions\FetchOne;
    use Actions\Store;
    use Actions\Update;
    use Actions\Destroy;
    use Actions\FetchRelated;
    use Actions\FetchRelationship;
    use Actions\UpdateRelationship;
    use Actions\AttachRelationship;
    use Actions\DetachRelationship;

    /**
     * Scan QR code and register attendance.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function scan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'event_id' => 'required|integer|exists:events,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $qrCode = $request->input('qr_code');
        $eventId = $request->input('event_id');

        // Find member by QR code
        $member = Member::where('qr_code', $qrCode)->first();

        if (!$member) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '404',
                        'title' => 'Member Not Found',
                        'detail' => 'No member found with the provided QR code.',
                    ],
                ],
            ], 404);
        }

        // Check if member is active
        if (!$member->is_active) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '403',
                        'title' => 'Member Inactive',
                        'detail' => 'The member is not active.',
                    ],
                ],
            ], 403);
        }

        // Find event
        $event = Event::find($eventId);

        if (!$event) {
            return response()->json([
                'errors' => [
                    [
                        'status' => '404',
                        'title' => 'Event Not Found',
                        'detail' => 'The specified event does not exist.',
                    ],
                ],
            ], 404);
        }

        // Determine status (present or late)
        $now = now();
        $arrivalTime = $now->format('H:i:s');
        
        // Combine event date and time
        $eventDate = Carbon::parse($event->date);
        $eventTime = Carbon::parse($event->time);
        $eventDateTime = Carbon::create(
            $eventDate->year,
            $eventDate->month,
            $eventDate->day,
            $eventTime->hour,
            $eventTime->minute,
            $eventTime->second
        );
        
        $status = 'present';
        $lateDetectionSettings = LateDetectionSetting::getInstance();

        if ($lateDetectionSettings->is_enabled && $lateDetectionSettings->auto_mark_late) {
            // Get threshold
            $thresholdMinutes = $lateDetectionSettings->default_threshold_minutes;
            
            if ($lateDetectionSettings->use_different_thresholds_by_type && $event->event_type_id) {
                $eventTypeThreshold = EventTypeLateThreshold::where('event_type_id', $event->event_type_id)->first();
                if ($eventTypeThreshold) {
                    $thresholdMinutes = $eventTypeThreshold->threshold_minutes;
                }
            }

            // Check if late (minutes after event start time)
            $minutesLate = $now->diffInMinutes($eventDateTime, false);
            if ($minutesLate > $thresholdMinutes) {
                $status = 'late';
            }
        }

        // Create or update attendance (unique on event_id + member_id)
        $attendance = Attendance::updateOrCreate(
            [
                'event_id' => $eventId,
                'member_id' => $member->id,
            ],
            [
                'status' => $status,
                'arrival_time' => $arrivalTime,
                'scanned_at' => $now,
                'recorded_by' => Auth::id(),
            ]
        );

        // Return JSON:API format response
        return response()->json([
            'data' => [
                'type' => 'attendances',
                'id' => (string) $attendance->id,
                'attributes' => [
                    'status' => $attendance->status,
                    'arrival_time' => $attendance->arrival_time?->format('H:i:s'),
                    'scanned_at' => $attendance->scanned_at?->toIso8601String(),
                    'created_at' => $attendance->created_at->toIso8601String(),
                    'updated_at' => $attendance->updated_at->toIso8601String(),
                ],
                'relationships' => [
                    'event' => [
                        'data' => [
                            'type' => 'events',
                            'id' => (string) $event->id,
                        ],
                    ],
                    'member' => [
                        'data' => [
                            'type' => 'members',
                            'id' => (string) $member->id,
                        ],
                    ],
                ],
            ],
        ], 201);
    }

}

