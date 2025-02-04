<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class Timetable extends Mailable
{
    use Queueable, SerializesModels;

    protected Collection $timetableEvents;
    protected Carbon $startDate;
    protected Carbon $endDate;

    public function __construct(Collection $timetableEvents, Carbon $startDate, Carbon $endDate) 
    {
        $this->timetableEvents = $timetableEvents;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function build()
    {
        return $this->markdown('emails.timetable')
                    ->subject('Weekly Timetable')
                    ->with([
                        'timetableEvents' => $this->timetableEvents,
                        'startDate' => $this->startDate,
                        'endDate' => $this->endDate,
                    ]);
    }
}