<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Webhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Telegram webhook';

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
        $url = env('URL').'/receive/'.env('WEBHOOK_TOKEN');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.env('BOT_TOKEN').'/setWebhook');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('url' => $url));
        $json = curl_exec($ch);
        $result = json_decode($json, true);
        if($result['ok'])
            $this->info('Webhook was set to '.$url);
        else
            $this->error($json);

    }
}
