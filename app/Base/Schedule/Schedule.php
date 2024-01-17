<?php

namespace App\Base\Schedule;

use Throwable;
use Illuminate\Console\Scheduling\Schedule as SystemSchedule;

class Schedule
{
    /**
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * Schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @throws \Throwable
     */
    public function run(SystemSchedule $schedule)
    {
        if (config('app_main.is_off_system')) {
            return;
        }

        $this->schedule = $schedule;

        /**
         * System.
         */
        $this->system();

        /**
         * CRM.
         */
        $this->crm();

        /**
         * Sample.
         */
        //$this->call(function () {
        //
        //})->everyFifteenMinutes();

    }

    /**
     * System.
     */
    public function system()
    {
        /**
         * Horizon.
         */
        $this->schedule->command('horizon:snapshot')->everyFiveMinutes();

    }

    /**
     * CRM.
     */
    public function crm()
    {
        /**
         * Обновление токенов AmoCRM.
         */
        $this->schedule->command('crm:update-crm-tokens')->cron('0 */12 * * *');
    }

    //****************************************************************
    //************************** Support *****************************
    //****************************************************************

    /**
     * Задача планировщика.
     *
     * @param callable $callable
     * @return \Illuminate\Console\Scheduling\CallbackEvent
     */
    protected function call(callable $callable)
    {
        return $this->schedule->call(function () use ($callable) {
            try {
                $callable();
            } catch (Throwable $e) {
                alt_log()->file('error_schedule_task')->exception($e);
            }
        });
    }

}
