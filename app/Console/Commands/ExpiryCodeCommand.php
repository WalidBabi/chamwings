<?php

namespace App\Console\Commands;

use App\Models\VerifyAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpiryCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:code';

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
        $verifies = VerifyAccount::all();

        foreach ($verifies as $verify) {
            if ($verify && $verify->created_at < Carbon::now()) {
                $verify->delete();
            }
        }
        return Command::SUCCESS;
    }
}