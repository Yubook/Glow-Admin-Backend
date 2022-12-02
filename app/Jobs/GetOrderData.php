<?php

namespace App\Jobs;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetOrderData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $stripeTranaction;
    private $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($stripeTranaction, $order)
    {
        $this->stripeTranaction = $stripeTranaction;
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /// get payment full details
        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET_KEY')
        );
        $data = $stripe->balanceTransactions->retrieve(
            $this->stripeTranaction,
            []
        );

        $order_main_price = $data->amount / 100; // Total order price
        $stripe_fee = $data->fee / 100;
        $admin_fee = ($data->net / 100) * 0.05;  // 5% of admin commission
        $barber_amount = ($data->net / 100) - $admin_fee;

        $order = Order::find($this->order->id);
        $order->stripe_fee =  $stripe_fee;
        $order->admin_fee = $admin_fee;
        $order->net_order_price = $data->net / 100;
        $order->barber_amount = $barber_amount;
        $order->save();

        \Log::info($data);
        \Log::info($order);
    }
}
