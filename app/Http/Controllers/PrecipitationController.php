<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PrecipitationController extends Controller
{
    public function index()
    {
        $lat = 51.2194;
        $lon = 4.4025;

        try {
            // Huidige weer + 7 dagen
            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lon,
                'current' => 'precipitation,rain,temperature_2m',
                'hourly' => 'precipitation,precipitation_probability',
                'daily' => 'precipitation_sum,precipitation_hours,precipitation_probability_max',
                'timezone' => 'Europe/Brussels',
                'forecast_days' => 7,
            ]);

            $data = $response->json();
            $current = $data['current'] ?? [];

            // Komende 24 uur
            $hourlyTimes = $data['hourly']['time'] ?? [];
            $hourlyPrecip = $data['hourly']['precipitation'] ?? [];
            $hourlyProb = $data['hourly']['precipitation_probability'] ?? [];

            $nowHour = now()->format('Y-m-d\TH:00');
            $startIdx = array_search($nowHour, $hourlyTimes) ?: 0;

            $hoursToday = [];
            for ($i = $startIdx; $i < $startIdx + 24 && $i < count($hourlyTimes); $i++) {
                $hoursToday[] = [
                    'time' => Carbon::parse($hourlyTimes[$i])->format('H:i'),
                    'precip' => round($hourlyPrecip[$i] ?? 0, 1),
                    'probability' => $hourlyProb[$i] ?? 0,
                ];
            }

            // 7 dagen
            $dailyTimes = $data['daily']['time'] ?? [];
            $dailyPrecip = $data['daily']['precipitation_sum'] ?? [];
            $dailyHours = $data['daily']['precipitation_hours'] ?? [];
            $dailyProb = $data['daily']['precipitation_probability_max'] ?? [];

            $days = [];
            foreach ($dailyTimes as $i => $date) {
                $days[] = [
                    'date' => Carbon::parse($date)->isoFormat('dd D MMM'),
                    'precip_sum' => round($dailyPrecip[$i] ?? 0, 1),
                    'precip_h' => round($dailyHours[$i] ?? 0),
                    'probability' => $dailyProb[$i] ?? 0,
                ];
            }

            // Week statistieken
            $weekTotal = round(array_sum($dailyPrecip), 1);
            $weekAvg = count($dailyPrecip) > 0 ? round($weekTotal / count($dailyPrecip), 1) : 0;
            $rainyDays = count(array_filter($dailyPrecip, fn ($v) => $v > 0));

            // Komende 16 dagen groepeer per week
            $startDate = now()->format('Y-m-d');
            $endDate = now()->addDays(15)->format('Y-m-d');

            $monthForecastRes = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'precipitation_sum',
                'timezone' => 'Europe/Brussels',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $monthForecastData = $monthForecastRes->json();
            $forecastTimes = $monthForecastData['daily']['time'] ?? [];
            $forecastPrecip = $monthForecastData['daily']['precipitation_sum'] ?? [];

            // Groepeer per week
            $monthlyForecast = [];
            foreach ($forecastTimes as $i => $date) {
                $weekNum = 'Week '.Carbon::parse($date)->weekOfYear;
                $weekStart = Carbon::parse($date)->startOfWeek()->isoFormat('D MMM');
                $weekEnd = Carbon::parse($date)->endOfWeek()->isoFormat('D MMM');
                $weekKey = $weekNum;
                $weekLabel = $weekStart.' – '.$weekEnd;

                if (! isset($monthlyForecast[$weekKey])) {
                    $monthlyForecast[$weekKey] = [
                        'name' => $weekLabel,
                        'total' => 0,
                        'days' => 0,
                        'rainy' => 0,
                    ];
                }
                $val = $forecastPrecip[$i] ?? 0;
                $monthlyForecast[$weekKey]['total'] += $val;
                $monthlyForecast[$weekKey]['days']++;
                if ($val > 0) {
                    $monthlyForecast[$weekKey]['rainy']++;
                }
            }

            foreach ($monthlyForecast as &$m) {
                $m['total'] = round($m['total'], 1);
                $m['avg'] = $m['days'] > 0 ? round($m['total'] / $m['days'], 1) : 0;
            }

            $monthlyAvg = array_values($monthlyForecast);

            // ── Neerslag-gebaseerde productsuggesties ─────────────────────
            $currentPrecip  = (float) ($current['precipitation'] ?? 0);
            $todayTotal     = (float) ($days[0]['precip_sum'] ?? 0);
            $tomorrowTotal  = (float) ($days[1]['precip_sum'] ?? 0);
            $maxPrecip      = max($currentPrecip, $todayTotal, $tomorrowTotal);

            // Intensiteitsniveau bepalen
            if ($maxPrecip >= 10) {
                $rainLevel = 'zwaar';       // ≥10 mm
            } elseif ($maxPrecip >= 3) {
                $rainLevel = 'matig';       // 3–10 mm
            } elseif ($maxPrecip > 0) {
                $rainLevel = 'licht';       // 0–3 mm
            } else {
                $rainLevel = 'droog';
            }

            // Categorieën per niveau
            $categoryPriority = match ($rainLevel) {
                'zwaar'  => ['Leidingen & Koppelingen', 'Gereedschap & Machines', 'Inspectie & Meting', 'Verbruiksmaterialen'],
                'matig'  => ['Leidingen & Koppelingen', 'Verbruiksmaterialen', 'Gereedschap & Machines'],
                'licht'  => ['Verbruiksmaterialen', 'Leidingen & Koppelingen'],
                default  => [],
            };

            $recommendedProducts = collect();
            if (! empty($categoryPriority)) {
                $catIds = ProductCategory::whereIn('name', $categoryPriority)->pluck('id');
                $recommendedProducts = Product::whereIn('product_category_id', $catIds)
                    ->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->inRandomOrder()
                    ->limit(6)
                    ->get();
            }

        } catch (\Exception $e) {
            return view('neerslag.index', ['error' => 'Fout: '.$e->getMessage()]);
        }

        return view('neerslag.index', compact(
            'current', 'hoursToday', 'days',
            'weekTotal', 'weekAvg', 'rainyDays', 'monthlyAvg',
            'rainLevel', 'recommendedProducts'
        ));
    }
}
