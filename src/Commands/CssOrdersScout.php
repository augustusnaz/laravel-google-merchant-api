<?php

namespace MOIREI\GoogleMerchantApi\Commands;

use Illuminate\Console\Command;
use MOIREI\GoogleMerchantApi\Facades\OrderApi;

class CssOrdersScout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gm-orders:scout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired carts';

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
        OrderApi::scout();
    }
}
