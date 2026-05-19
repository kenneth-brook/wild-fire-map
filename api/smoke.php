<?php

$config = require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';

$cacheName = 'smoke';

if ($cached = get_cached($cacheName, $config['cache_ttl'])) {
    send_json($cached);
}

try {
    $url = arcgis_geojson_url($config['smoke_url'], $config['bbox']);
    $raw = http_get($url);
    $data = normalize_feature_collection(json_decode($raw, true) ?: []);

    set_cached($cacheName, $data);
    send_json($data);
} catch (Throwable $e) {
    send_json(empty_geojson(), 200);
}