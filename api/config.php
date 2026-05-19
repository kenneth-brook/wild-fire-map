<?php

return [
    'cache_ttl' => 300, // 5 minutes

    'bbox' => [
        'west' => -85.65,
        'south' => 30.30,
        'east' => -80.75,
        'north' => 35.05,
    ],

    'firms_map_key' => 'KEY_GOES_HERE',

    'wfigs_incidents_url' =>
        'https://services3.arcgis.com/T4QMspbfLg3qTGWY/arcgis/rest/services/WFIGS_Incident_Locations_Current/FeatureServer/0/query',

    'wfigs_perimeters_url' =>
        'https://services3.arcgis.com/T4QMspbfLg3qTGWY/arcgis/rest/services/WFIGS_Interagency_Perimeters_Current/FeatureServer/0/query',

    // NOAA HMS Smoke Detection service
    'smoke_url' =>
        'https://services2.arcgis.com/C8EMgrsFcRFL6LrL/arcgis/rest/services/NOAA_Satellite_Smoke_Detection_(v1)/FeatureServer/0/query',
];