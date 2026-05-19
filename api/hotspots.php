<?php

$config = require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';

$cacheName = 'hotspots';

if ($cached = get_cached($cacheName, $config['cache_ttl'])) {
    send_json($cached);
}

function csv_to_geojson(string $csv): array
{
    $lines = array_filter(array_map('trim', explode("\n", $csv)));

    if (count($lines) < 2) {
        return empty_geojson();
    }

    $headers = str_getcsv(array_shift($lines));
    $features = [];

    foreach ($lines as $line) {
        $row = str_getcsv($line);

        if (count($row) !== count($headers)) {
            continue;
        }

        $record = array_combine($headers, $row);

        if (!isset($record['longitude'], $record['latitude'])) {
            continue;
        }

        $lng = (float) $record['longitude'];
        $lat = (float) $record['latitude'];

        $features[] = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$lng, $lat],
            ],
            'properties' => $record,
        ];
    }

    return [
        'type' => 'FeatureCollection',
        'features' => $features,
    ];
}

try {
    $bbox = $config['bbox'];

    $bboxString = implode(',', [
        $bbox['west'],
        $bbox['south'],
        $bbox['east'],
        $bbox['north'],
    ]);

    $mapKey = rawurlencode($config['firms_map_key']);

    // VIIRS_SNPP_NRT, past 1 day
    $url = "https://firms.modaps.eosdis.nasa.gov/api/area/csv/{$mapKey}/VIIRS_SNPP_NRT/{$bboxString}/1";

    $csv = http_get($url);
    $data = csv_to_geojson($csv);

    set_cached($cacheName, $data);
    send_json($data);
} catch (Throwable $e) {
    send_json(empty_geojson(), 200);
}