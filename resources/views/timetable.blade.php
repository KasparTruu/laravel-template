<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tunniplaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">
            Tunniplaan {{ $startDate->format('d.m.Y') }} - {{ $endDate->format('d.m.Y') }}
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach($timetableEvents as $day => $events)
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-4">{{ $day }}</h2>
                    
                    <div class="space-y-3">
                        @foreach($events as $event)
                            <div class="border-l-4 border-yellow-400 pl-3">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $event['timeStart'] }}-{{ $event['timeEnd'] }}
                                </div>
                                <div class="text-sm text-gray-700">
                                    {{ $event['nameEt'] }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $event['rooms'][0]['roomCode'] ?? 'TBA' }} |
                                    {{ $event['teachers'][0]['name'] ?? 'TBA' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>