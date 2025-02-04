<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Mail\Timetable;

class TimetableNotification extends Command
{
    protected $signature = 'app:timetable-notification';
    protected $description = 'Send timetable notification email';
    protected $maxRetries = 3;

    public function handle()
    {
        try {
            $baseUrl = Config::get('services.timetable.url', 'https://tahvel.edu.ee/hois_back/timetableevents/timetableByGroup/38');
            
            $startDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $endDate = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            $queryParams = [
                'from' => $startDate->toIsoString(),
                'studentGroups' => Config::get('services.timetable.group_id', '7596'),
                'thru' => $endDate->toIsoString(),
            ];

            $response = $this->makeRequestWithRetry($baseUrl, $queryParams);
            
            if (!$response->successful()) {
                throw new \Exception('Failed to fetch timetable data: ' . $response->status());
            }

            $data = $response->json();
            
            if (empty($data['timetableEvents'])) {
                Log::warning('No timetable events found for the week');
                $this->warn('No timetable events found for the week');
                return;
            }

            $timetableEvents = collect($data['timetableEvents'])
                ->sortBy(function ($event) {
                    return [$event['date'], $event['timeStart']];
                })
                ->groupBy(function ($event) {
                    $date = Carbon::parse($event['date']);
                    if ($date) {
                        if ($date instanceof Carbon) {
                            $date->setLocale('et_EE');
                            return $date->dayName;
                        }
                        return 'Invalid date';
                    }
                    return 'Invalid date';
                });

            if ($timetableEvents->isEmpty()) {
                Log::info('No events to send');
                $this->info('No events to send');
                return;
            }

            Mail::to(Config::get('mail.recipient_address'))
                ->send(new Timetable($timetableEvents, $startDate, $endDate));

            Log::info('Timetable notification sent successfully');
            $this->info('Timetable notification sent successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to process timetable notification: ' . $e->getMessage());
            $this->error('Failed to process timetable notification: ' . $e->getMessage());
            return 1;
        }
    }

    protected function makeRequestWithRetry($url, $params)
    {
        $attempt = 1;
        
        while ($attempt <= $this->maxRetries) {
            try {
                $response = Http::timeout(30)->get($url, $params);
                if ($response->successful()) {
                    return $response;
                }
            } catch (\Exception $e) {
                Log::warning("Request attempt {$attempt} failed: " . $e->getMessage());
            }
            
            if ($attempt < $this->maxRetries) {
                sleep(pow(2, $attempt));
            }
            
            $attempt++;
        }
        
        throw new \Exception("Failed to fetch data after {$this->maxRetries} attempts");
    }
}
