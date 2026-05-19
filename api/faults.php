<?php

$config = require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';

$cacheName = 'faults';
$faultCacheTtl = 86400;
$pageSize = 2000;
$maxPages = 20; // safety cap: 20 * 2000 = 40,000 features

if ($cached = get_cached($cacheName, $faultCacheTtl)) {
    send_json($cached);
}

function arcgis_geojson_url_paged(string $baseUrl, array $bbox, int $offset, int $limit): string
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

        // Pagination
        'orderByFields' => 'OBJECTID ASC',
        'resultOffset' => $offset,
        'resultRecordCount' => $limit,
    ]);

    return $baseUrl . '?' . $query;
}

try {
    $allFeatures = [];
    $offset = 0;

    for ($page = 0; $page < $maxPages; $page++) {
        $url = arcgis_geojson_url_paged(
            $config['usgs_faults_url'],
            $config['us_bbox'],
            $offset,
            $pageSize
        );

        $raw = http_get($url);
        $data = normalize_feature_collection(json_decode($raw, true) ?: []);
        $features = $data['features'] ?? [];

        if (count($features) === 0) {
            break;
        }

        $allFeatures = array_merge($allFeatures, $features);

        if (count($features) < $pageSize) {
            break;
        }

        $offset += $pageSize;
    }

    $merged = [
        'type' => 'FeatureCollection',
        'features' => $allFeatures,
        'metadata' => [
            'source' => 'USGS Qfaults',
            'feature_count' => count($allFeatures),
            'paged' => true,
            'page_size' => $pageSize,
        ],
    ];

    set_cached($cacheName, $merged);
    send_json($merged);
} catch (Throwable $e) {
    send_json([
        'type' => 'FeatureCollection',
        'features' => [],
        'error' => $e->getMessage(),
    ], 200);
}