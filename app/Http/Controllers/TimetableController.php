<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Illuminate\Support\Collection;
use Illuminate\Http\Client\Response;

class TimetableController extends Controller
{
    public function index(): View
    {
        try {
            $startDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $endDate = Carbon::now()->endOfWeek(Carbon::SUNDAY);

            $response = $this->fetchTimetableData($startDate, $endDate);
            
            if (!isset($response['timetableEvents'])) {
                throw new \Exception('Invalid timetable data format');
            }

            $timetableEvents = $this->processTimetableEvents($response['timetableEvents']);

            return view('timetable.index', compact('timetableEvents', 'startDate', 'endDate'));
        } catch (\Exception $e) {
            return view('timetable.error', ['message' => $e->getMessage()]);
        }
    }

    private function fetchTimetableData(Carbon $startDate, Carbon $endDate): array
    {
        $response = Http::get(config('services.timetable.url'), [
            'from' => $startDate->toIsoString(),
            'studentGroups' => config('services.timetable.group_id'),
            'thru' => $endDate->toIsoString(),
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch timetable data');
        }

        return $response->json();
    }

    private function processTimetableEvents(array $events): Collection
    {
        return collect($events)
            ->sortBy(['date', 'timeStart'])
            ->groupBy(function ($event) {
                return Carbon::parse($event['date'])
                    ->locale('et_EE')
                    ->dayName;
            });
    }
}