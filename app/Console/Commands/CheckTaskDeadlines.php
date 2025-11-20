<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CheckTaskDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate notifications for tasks approaching or past their deadlines';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        NotificationService::checkDeadlines();
        $this->info('Deadline notifications generated.');
        return Command::SUCCESS;
    }
}
