<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * @var string
     */
    protected $description = 'Тест для дебага';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        dd(app(\App\Base\Project\Project::class)->getProject('guldog'));

        dd(app(\App\Base\Crm\AmoCrm\AmoCrm::class)->getLead([
            'project_slug' => 'guldog',
            'lead_id' => 31774267,
            'with' => ['contacts'],
        ]));
        dd(app(\App\Base\Crm\AmoCrm\AmoCrm::class)->getContact([
            'project_slug' => 'guldog',
            'search' => '+79999999999',
            'with' => ['leads'],
        ]));
    }
}
