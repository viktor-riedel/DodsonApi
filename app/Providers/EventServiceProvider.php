<?php

namespace App\Providers;

use App\Events\Bot\SendBotMessageEvent;
use App\Events\ModelsEvent\NomenclatureCreateEvent;
use App\Events\Sync\Export\SendToBotEvent;
use App\Listeners\Bot\SendBotMessageListener;
use App\Listeners\NomenclatureCreatedListener;
use App\Listeners\SendCarToBotListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        NomenclatureCreateEvent::class => [
            NomenclatureCreatedListener::class,
        ],
        SendToBotEvent::class => [
            SendCarToBotListener::class,
        ],
        SendBotMessageEvent::class => [
            SendBotMessageListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
