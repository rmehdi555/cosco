<?php

return [
    /*
    |--------------------------------------------------------------------------
    | General Application Settings
    |--------------------------------------------------------------------------
    |
    | This file contains general application settings and helper functions
    | that can be used throughout the application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Price Display Settings
    |--------------------------------------------------------------------------
    |
    | These settings control how prices are displayed in the application.
    | By default, prices are stored in Rial and displayed in Toman.
    |
    */
    'currency' => [
        'base_unit' => 'rial',        // Base currency unit stored in database
        'display_unit' => 'toman',    // Display currency unit
        'conversion_rate' => 10,      // 1 Toman = 10 Rial
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Functions
    |--------------------------------------------------------------------------
    |
    | These functions can be accessed globally using config('general.show_price')
    |
    */
    'show_price' => function ($price) {
        if ($price === null || $price === '') {
            return 0;
        }
        
        // Convert Rial to Toman by dividing by 10 (removing one zero)
        $tomanPrice = $price / 10;
        
        // Return formatted price as integer if it's a whole number, otherwise with decimals
        return $tomanPrice == floor($tomanPrice) ? (int) $tomanPrice : $tomanPrice;
    },

    /*
    |--------------------------------------------------------------------------
    | Price Formatting Function
    |--------------------------------------------------------------------------
    |
    | Format price with thousand separators and currency unit
    |
    */
    'format_price' => function ($price, $showUnit = true) {
        $tomanPrice = config('general.show_price')($price);
        $formatted = number_format($tomanPrice);
        
        return $showUnit ? $formatted . ' تومان' : $formatted;
    },

    /*
    |--------------------------------------------------------------------------
    | Reverse Price Conversion
    |--------------------------------------------------------------------------
    |
    | Convert Toman back to Rial for database storage
    |
    */
    'to_rial' => function ($tomanPrice) {
        if ($tomanPrice === null || $tomanPrice === '') {
            return 0;
        }
        
        // Convert Toman to Rial by multiplying by 10 (adding one zero)
        return $tomanPrice * 10;
    },

    /*
    |--------------------------------------------------------------------------
    | Date Conversion Functions
    |--------------------------------------------------------------------------
    |
    | Convert Gregorian dates to Persian (Shamsi) dates using Verta
    |
    */
    'show_date' => function ($date, $format = 'Y-m-d') {
        if ($date === null || $date === '') {
            return null;
        }
        
        try {
            // Convert to Carbon instance if it's a string
            if (is_string($date)) {
                $date = \Carbon\Carbon::parse($date);
            }
            
            // Convert to Verta (Persian date)
            $verta = \Hekmatinasser\Verta\Verta::instance($date);
            
            return $verta->format($format);
        } catch (\Exception $e) {
            return null;
        }
    },

    /*
    |--------------------------------------------------------------------------
    | Date Formatting with Time
    |--------------------------------------------------------------------------
    |
    | Convert Gregorian datetime to Persian with custom format
    |
    */
    'show_datetime' => function ($datetime, $format = 'Y-m-d H:i') {
        return config('general.show_date')($datetime, $format);
    },

    /*
    |--------------------------------------------------------------------------
    | Persian Date with Persian Month Names
    |--------------------------------------------------------------------------
    |
    | Convert to Persian date with full Persian month names
    |
    */
    'show_date_persian' => function ($date) {
        if ($date === null || $date === '') {
            return null;
        }
        
        try {
            // Convert to Carbon instance if it's a string
            if (is_string($date)) {
                $date = \Carbon\Carbon::parse($date);
            }
            
            // Convert to Verta (Persian date)
            $verta = \Hekmatinasser\Verta\Verta::instance($date);
            
            return $verta->format('d F Y');
        } catch (\Exception $e) {
            return null;
        }
    },
];
