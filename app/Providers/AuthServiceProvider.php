<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Member;
use App\Models\QRCodeDistribution;
use App\Models\QRCodeGenerationHistory;
use App\Models\RecurringEventSchedule;
use App\Models\User;
use App\Policies\AttendancePolicy;
use App\Policies\EventPolicy;
use App\Policies\EventTypePolicy;
use App\Policies\MemberPolicy;
use App\Policies\QRCodeDistributionPolicy;
use App\Policies\QRCodeGenerationHistoryPolicy;
use App\Policies\RecurringEventSchedulePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Member::class => MemberPolicy::class,
        Event::class => EventPolicy::class,
        Attendance::class => AttendancePolicy::class,
        EventType::class => EventTypePolicy::class,
        QRCodeDistribution::class => QRCodeDistributionPolicy::class,
        QRCodeGenerationHistory::class => QRCodeGenerationHistoryPolicy::class,
        RecurringEventSchedule::class => RecurringEventSchedulePolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
