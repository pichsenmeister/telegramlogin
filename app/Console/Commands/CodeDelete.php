<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CodeDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old codes';

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
        $cliff = (new \DateTime())->modify('-24 hours');
        \App\Code::where('created_at', '<', $cliff)->delete();
    }
}
