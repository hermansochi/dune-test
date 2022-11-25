<?php

namespace App\Console\Commands;

use App\Services\DivergeChecker;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $checker = new DivergeChecker();
        $result = $checker->setThreshold(10)->diffPrice(6.66, 11);
        $deviation = $checker->getDeviation();

        $this->line('Check: '.$result);
        $this->line('Deviation: '.$deviation);

        return Command::SUCCESS;
    }
}
