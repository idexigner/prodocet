<?php

if (!function_exists('client_config')) {
    /**
     * Get client configuration value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function client_config($key = null, $default = false)
    {
        $clientId = env('CLIENT_ID', 'saffron');
        $config = config('clients.' . $clientId);
        
        if (is_null($key)) {
            return $config;
        }

        // Check if the key exists in the config
        if (!data_get($config, $key)) {
            return $default;
        }

        return data_get($config, $key);
    }
} 

