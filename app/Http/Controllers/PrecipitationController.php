<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class PrecipitationController extends Controller
{
    public function index()
    {
        $lat = 51.2194;
        $lon = 4.4025;

        try {
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude'      => $lat,
                'longitude'     => $lon,
                'current'       => 'precipitation,rain,temperature_2m',
                'hourly'        => 'precipitation,precipitation_probability',
                'daily'         => 'precipitation_sum,precipitation_hours,precipitation_probability_max',
                'timezone'      => 'Europe/Brussels',
                'forecast_days' => 7,
            ]);

            $data = $response->json();
            $current = $data['current'] ?? [];

            // Komende 24 uur
            $hourlyTimes  = $data['hourly']['time'] ?? [];
            $hourlyPrecip = $data['hourly']['precipitation'] ?? [];
            $hourlyProb   = $data['hourly']['precipitation_probability'] ?? [];

            $nowHour  = now()->format('Y-m-d\TH:00');
            $startIdx = array_search($nowHour, $hourlyTimes) ?: 0;

            $hoursToday = [];
            for ($i = $startIdx; $i < $startIdx + 24 && $i < count($hourlyTimes); $i++) {
                $hoursToday[] = [
                    'time'        => \Carbon\Carbon::parse($hourlyTimes[$i])->format('H:i'),
                    'precip'      => round($hourlyPrecip[$i] ?? 0, 1),
                    'probability' => $hourlyProb[$i] ?? 0,
                ];
            }

            // 7 dagen
            $dailyTimes  = $data['daily']['time'] ?? [];
            $dailyPrecip = $data['daily']['precipitation_sum'] ?? [];
            $dailyHours  = $data['daily']['precipitation_hours'] ?? [];
            $dailyProb   = $data['daily']['precipitation_probability_max'] ?? [];

            $days = [];
            foreach ($dailyTimes as $i => $date) {
                $days[] = [
                    'date'        => \Carbon\Carbon::parse($date)->isoFormat('dd D MMM'),
                    'precip_sum'  => round($dailyPrecip[$i] ?? 0, 1),
                    'precip_h'    => round($dailyHours[$i] ?? 0),
                    'probability' => $dailyProb[$i] ?? 0,
                ];
            }

            // Week statistieken
            $weekTotal = round(array_sum($dailyPrecip), 1);
            $weekAvg   = count($dailyPrecip) > 0 ? round($weekTotal / count($dailyPrecip), 1) : 0;
            $rainyDays = count(array_filter($dailyPrecip, fn($v) => $v > 0));

            // Maandgemiddelden via historische data (2016-2025)
            $monthTotals = array_fill(0, 12, 0);
            $monthCounts = array_fill(0, 12, 0);

            $archiveRes = Http::get('https://archive-api.open-meteo.com/v1/archive', [
                'latitude'   => $lat,
                'longitude'  => $lon,
                'start_date' => '2016-01-01',
                'end_date'   => '2025-12-31',
                'monthly'    => 'precipitation_sum',
                'timezone'   => 'Europe/Brussels',
            ]);

            $archiveData = $archiveRes->json();

            foreach ($archiveData['monthly']['time'] ?? [] as $i => $t) {
                $m = (int)\Carbon\Carbon::parse($t)->format('n') - 1;
                $val = $archiveData['monthly']['precipitation_sum'][$i] ?? 0;
                if ($val > 0) {
                    $monthTotals[$m] += $val;
                    $monthCounts[$m]++;
                }
            }

            $monthlyAvg = [];
            for ($m = 0; $m < 12; $m++) {
                $monthlyAvg[] = $monthCounts[$m] > 0 ? round($monthTotals[$m] / $monthCounts[$m]) : 0;
            }

        } catch (\Exception $e) {
            return view('neerslag.index', ['error' => 'Gegevens ophalen mislukt.']);
        }

        return view('neerslag.index', compact(
            'current', 'hoursToday', 'days',
            'weekTotal', 'weekAvg', 'rainyDays', 'monthlyAvg'
        ));
    }
}