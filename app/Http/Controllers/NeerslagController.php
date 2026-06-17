<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NeerslagController extends Controller
{
    private float $lat = 51.2194;

    private float $lon = 4.4025;

    public function index()
    {
        $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $this->lat,
            'longitude' => $this->lon,
            'hourly' => 'precipitation,precipitation_probability',
            'daily' => 'precipitation_sum,precipitation_hours,precipitation_probability_max',
            'current' => 'precipitation,rain,temperature_2m,weathercode',
            'timezone' => 'Europe/Brussels',
            'forecast_days' => 7,
        ]);

        if ($response->failed()) {
            return view('neerslag.index', ['error' => 'API niet bereikbaar. Probeer later opnieuw.']);
        }

        $data = $response->json();
        $current = $data['current'] ?? [];
        $hourly = $data['hourly'] ?? [];
        $daily = $data['daily'] ?? [];

        $now = now()->setTimezone('Europe/Brussels');
        $hoursToday = [];

        foreach ($hourly['time'] as $i => $time) {
            $dt = Carbon::parse($time, 'Europe/Brussels');
            if ($dt->between($now, $now->copy()->addHours(24))) {
                $hoursToday[] = [
                    'time' => $dt->format('H:i'),
                    'precip' => $hourly['precipitation'][$i],
                    'probability' => $hourly['precipitation_probability'][$i],
                ];
            }
        }

        $days = [];
        foreach ($daily['time'] as $i => $date) {
            $days[] = [
                'date' => Carbon::parse($date)->locale('nl')->isoFormat('ddd D MMM'),
                'precip_sum' => $daily['precipitation_sum'][$i],
                'precip_h' => $daily['precipitation_hours'][$i],
                'probability' => $daily['precipitation_probability_max'][$i],
            ];
        }

        return view('neerslag.index', compact('current', 'hoursToday', 'days'));
    }
}
