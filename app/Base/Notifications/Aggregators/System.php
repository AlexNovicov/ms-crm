<?php

namespace App\Base\Notifications\Aggregators;

use App\Notifications\ErrorWithAmoTokens;
use ProfilanceGroup\BackendSdk\Tools\TimeOut;
use Illuminate\Support\Facades\Notification;

class System
{
    /**
     * Уведомление об ошибке авторизации в амо.
     *
     * @param int $code
     * @return void
     */
    public function aboutCrmAuthError($code)
    {
        if (
            empty(config('app_notifications.notify_about_crm_auth_errors_enabled'))
            || !app(TimeOut::class)->check('notification_about_crm_auth_Error', 60*60)
        ) {
            return;
        }

        Notification::route('slack', config('services.slack_pg_tech_alerts.url'))
            ->route('telegram', config('services.telegram-bot-api.token'))
            ->notify(new ErrorWithAmoTokens($code));
    }
}
