<?php

namespace App\Console\Commands;

use App\DriverSlot;
use App\Timing;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SlotCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slot:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic Weekly Timing Slot Generate';

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
     * @return int
     */
    public function handle()
    {
        // $drivers = User::where(['role_id' => 2, 'is_active' => 1])->pluck('id');
        // $timeSlots = Timing::where(['type' => 0, 'is_active' => 1])->get();
        // $date = Carbon::now();
        // foreach ($drivers as $key => $driver) {
        //     $days = [0, 1, 2, 3, 4, 5, 6];
        //     foreach ($days as $key => $day) {
        //         foreach ($timeSlots as $key => $timeSlot) {
        //             $add_slots = new DriverSlot();
        //             $add_slots->driver_id = $driver;
        //             $add_slots->date = $date->copy()->addDays($day)->format('Y-m-d');
        //             $add_slots->timing_id = $timeSlot->id;
        //             $add_slots->save();
        //         }
        //     }
        // }
        // $DeleteUnusedtimeSlots = DriverSlot::where(['user_id' => null, 'is_booked' => 0])->whereDate('updated_at', '<', Carbon::today()->subDays(15))->delete();
        // $this->info('Slot:Cron Command Run successfully!');
    }
}
