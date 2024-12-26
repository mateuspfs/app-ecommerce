<?php

namespace Config;

use CodeIgniter\Tasks\Config\Tasks as BaseTasks;
use CodeIgniter\Tasks\Scheduler;
use App\Tasks\CheckPaymentsTask;

class Tasks extends BaseTasks
{
    /**
     * Register any tasks within this method for the application.
     *
     * @param Scheduler $schedule
     */
    public function init(Scheduler $schedule)
    {
        $schedule->call(function () {
            $checkPaymentsTask = new CheckPaymentsTask();
            $checkPaymentsTask->checkPaymentsByMethod('pix');
        })->everyFiveMinutes();

        $schedule->call(function () {
            $checkPaymentsTask = new CheckPaymentsTask();
            $checkPaymentsTask->checkPaymentsByMethod('boleto');
        })->everyHour(3);

        $schedule->call(function () {
            $checkPaymentsTask = new CheckPaymentsTask();
            $checkPaymentsTask->checkPaymentsByMethod('credit-card');
        })->everyFiveMinutes();
    }
}
