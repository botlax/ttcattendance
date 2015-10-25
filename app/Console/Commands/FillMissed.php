<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Holiday;
use App\Trade;
use App\Site;
use App\Labor;
use App\Attendance;
use Carbon\Carbon;

class FillMissed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FillMissed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate missed attendance with Zero\'s';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //check if have entry for today
        //check if locked
        //if good kaliwali
        //else populate with zeroes
        //then lock
        if(Attendance::latest('att_date')->first()->att_date != Carbon::today()){
            $currentDate = new Attendance;
            $currentDate->att_date = Carbon::today();
            $holiday = 0;
            if(Carbon::today()->format('l') == 'Friday' || Holiday::where('holidate',Carbon::today())->first() != null){
                $holiday = 1;
            }
            $currentDate->holiday = $holiday;
            $currentDate->save();
        }

        foreach(Site::all() as $site){
            foreach($site->labor as $labor){
                if($labor->attendance()->where('att_date',Carbon::today()->format('Y-m-d G:i:s'))->first() != null){
                    $labor->attendance()->where('att_date',Carbon::today()->format('Y-m-d G:i:s'))->first()->pivot->update(['locked' => 'true']);
                }
                else{
                    Attendance::latest('att_date')->first()->labor()->attach($labor->id);
                    $att = $labor->attendance()->where('att_date',Carbon::today()->format('Y-m-d G:i:s'))->first();
                    $att->pivot->locked = 'true';
                    $att->pivot->attended = $att->holiday == 1 ? 1 : 0;
                    $att->pivot->ot = 0;
                    $att->pivot->bot = 0;
                    $att->pivot->site = $labor->site->code;
                    $att->pivot->save();
                }
            }
        }
    }
}
