<?php

/**
 * Default config
 * 
 * You can customize your config by creating a file "man-pagination.php" in the Laravel config folder.
 */

return [

    /**
     * For pagination
     */
    'text_next'     => config('man-pagination.text_next') ?? 'Next',
    'text_previous' => config('man-pagination.text_previous') ?? 'Previous',
    
    /**
     * For per page
     */
    'text_per_page' => config('man-pagination.text_per_page') ?? 'Per page',
    'text_all'      => config('man-pagination.text_all') ?? 'All',

];
