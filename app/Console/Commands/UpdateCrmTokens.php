<?php

namespace App\Console\Commands;

use App\Contracts\Crm;
use Illuminate\Console\Command;
use Throwable;

class UpdateCrmTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:update-crm-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление токенов CRM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            if (config('app.env') != 'production') {
                $this->error('Команда доступна только на проде, т.к. отзывает старые токены авторизации, что приведет к нарушению работы прода.');
                return;
            }

           app(Crm::class)->updateTokens();

        } catch (Throwable $e) {
            alt_log()->file('error_crm')->exception($e, 'Ошибка обновления токенов CRM.');
            $this->error($e->getMessage());
        }
    }
}
