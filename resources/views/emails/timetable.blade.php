<x-mail::message>
# Tunniplaan {{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}

@foreach($timetableEvents as $day => $events)
## {{ $day }}

<x-mail::table>
| Aeg | Aine | Ruum | Õppejõud |
| :-- | :--- | :--- | :------- |
@foreach($events as $event)
| {{ $event['timeStart'] }}-{{ $event['timeEnd'] }} | {{ $event['nameEt'] }} | {{ $event['rooms'][0]['roomCode'] ?? 'TBA' }} | {{ $event['teachers'][0]['name'] ?? 'TBA' }} |
@endforeach
</x-mail::table>

@endforeach

Täname,<br>
{{ config('app.name') }}
</x-mail::message>