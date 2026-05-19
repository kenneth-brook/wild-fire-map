<?php

return [
    'cache_ttl' => 300, // 5 minutes

    // Georgia bbox for wildfire, smoke, and hotspots
    'bbox' => [
        'west' => -85.65,
        'south' => 30.30,
        'east' => -80.75,
        'north' => 35.05,
    ],

    // Continental U.S. bbox for fault-line proof of concept
    'us_bbox' => [
        'west' => -94.00,
        'south' => 24.00,
        'east' => -75.00,
        'north' => 39.50,
    ],

    'firms_map_key' => 'KEY_GOES_HERE',

    'wfigs_incidents_url' =>
        'https://services3.arcgis.com/T4QMspbfLg3qTGWY/arcgis/rest/services/WFIGS_Incident_Locations_Current/FeatureServer/0/query',

    'wfigs_perimeters_url' =>
        'https://services3.arcgis.com/T4QMspbfLg3qTGWY/arcgis/rest/services/WFIGS_Interagency_Perimeters_Current/FeatureServer/0/query',

    'smoke_url' =>
        'https://services2.arcgis.com/C8EMgrsFcRFL6LrL/arcgis/rest/services/NOAA_Satellite_Smoke_Detection_(v1)/FeatureServer/0/query',

    'usgs_earthquakes_url' =>
        'https://earthquake.usgs.gov/earthquakes/feed/v1.0/summary/all_month.geojson',

    'usgs_faults_url' =>
        'https://earthquake.usgs.gov/arcgis/rest/services/haz/Qfaults/MapServer/21/query',
];