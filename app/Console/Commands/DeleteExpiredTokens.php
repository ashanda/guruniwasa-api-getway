<?php

namespace App\Console\Commands;

use App\Models\AuthToken;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired tokens older than 1 day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       $threshold = Carbon::now()->subDay();// Calculate the threshold (1 day ago)
        
        // Replace 'Token' with your actual model representing tokens
        AuthToken::where('created_at', '<', $threshold)->delete();

        $this->info('Expired tokens older than 1 day have been deleted.');
    }
}
