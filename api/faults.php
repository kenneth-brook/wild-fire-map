<?php

$config = require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';

$cacheName = 'faults';
$faultCacheTtl = 86400;

if ($cached = get_cached($cacheName, $faultCacheTtl)) {
    send_json($cached);
}

try {
    $url = arcgis_geojson_url($config['usgs_faults_url'], $config['us_bbox']);
    $raw = http_get($url);
    $data = normalize_feature_collection(json_decode($raw, true) ?: []);

    set_cached($cacheName, $data);
    send_json($data);
} catch (Throwable $e) {
    send_json(empty_geojson(), 200);
}