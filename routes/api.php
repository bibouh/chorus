<?php

use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\EventTypeController;
use App\Http\Controllers\Api\V1\MemberController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Routing\ActionRegistrar;
use LaravelJsonApi\Laravel\Routing\Relationships;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;

// Custom authentication routes
Route::post('/auth/login', [UserController::class, 'login']);

JsonApiRoute::server('v1')
    ->resources(function (ResourceRegistrar $server) {
        $server->resource('members', MemberController::class)
            ->relationships(function (Relationships $relationships) {
                $relationships->hasMany('attendances')->readOnly();
                $relationships->hasMany('qrCodeDistributions')->readOnly();
            });

        $server->resource('event-types', EventTypeController::class)
            ->relationships(function (Relationships $relationships) {
                $relationships->hasMany('events')->readOnly();
                $relationships->hasOne('lateThreshold')->readOnly();
            });

        $server->resource('events', EventController::class)
            ->relationships(function (Relationships $relationships) {
                $relationships->hasOne('eventType');
                $relationships->hasOne('creator')->readOnly();
                $relationships->hasOne('parentEvent')->readOnly();
                $relationships->hasMany('childEvents')->readOnly();
                $relationships->hasMany('attendances')->readOnly();
                $relationships->hasMany('recurringEventSchedules')->readOnly();
            });

        $server->resource('attendances', AttendanceController::class)
            ->relationships(function (Relationships $relationships) {
                $relationships->hasOne('event');
                $relationships->hasOne('member');
                $relationships->hasOne('recorder')->readOnly();
            });

        $server->resource('users', UserController::class)
            ->relationships(function (Relationships $relationships) {
                $relationships->hasMany('createdEvents')->readOnly();
                $relationships->hasMany('recordedAttendances')->readOnly();
            });
    });

