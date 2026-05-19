<?php

function send_json($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/geo+json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

function empty_geojson(): array
{
    return [
        'type' => 'FeatureCollection',
        'features' => [],
    ];
}

function cache_path(string $name): string
{
    return __DIR__ . '/../cache/' . $name . '.geojson';
}

function get_cached(string $name, int $ttl): ?array
{
    $path = cache_path($name);

    if (!file_exists($path)) {
        return null;
    }

    if ((time() - filemtime($path)) > $ttl) {
        return null;
    }

    $json = file_get_contents($path);
    $data = json_decode($json, true);

    return is_array($data) ? $data : null;
}

function set_cached(string $name, array $data): void
{
    $path = cache_path($name);
    $dir = dirname($path);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    file_put_contents($path, json_encode($data, JSON_UNESCAPED_SLASHES));
}

function http_get(string $url): string
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'DeKalb Wildfire Map/1.0',
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false || $status >= 400) {
        throw new RuntimeException($error ?: "HTTP request failed with status {$status}");
    }

    return $response;
}

function arcgis_geojson_url(string $baseUrl, array $bbox): string
{
    $geometry = [
        'xmin' => $bbox['west'],
        'ymin' => $bbox['south'],
        'xmax' => $bbox['east'],
        'ymax' => $bbox['north'],
        'spatialReference' => [
            'wkid' => 4326,
        ],
    ];

    $query = http_build_query([
        'where' => '1=1',
        'outFields' => '*',
        'f' => 'geojson',
        'returnGeometry' => 'true',
        'spatialRel' => 'esriSpatialRelIntersects',
        'geometryType' => 'esriGeometryEnvelope',
        'inSR' => '4326',
        'outSR' => '4326',
        'geometry' => json_encode($geometry),
    ]);

    return $baseUrl . '?' . $query;
}

function normalize_feature_collection(array $data): array
{
    if (($data['type'] ?? null) === 'FeatureCollection') {
        return $data;
    }

    return empty_geojson();
}