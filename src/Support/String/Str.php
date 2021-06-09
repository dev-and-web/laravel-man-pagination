<?php

namespace LaravelManPagination\Support\String;

use LaravelManPagination\Pagination;
use Illuminate\Support\Facades\Request as RequestLF;

/**
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
class Str
{
    /**
     * @param array $options 
     *     $options['except'] (array) : the query strings to keep in the form.
     * @return string
     */
    public static function inputHiddenIfHasQueryString(array $options = []): string
    {
        $arrayExcept = isset($options['except']) ? $options['except'] : [];
        
        $htmlInputs = '';

        $arrayToIgnore = array_merge([Pagination::PAGE_NAME], (array) $arrayExcept);

        foreach (RequestLF::all() as $get => $v) {
            if (!in_array($get, $arrayToIgnore)) {
                if (is_array($v)) {
                    foreach ($v as $k => $oneV) {
                        $htmlInputs .= '<input type="hidden" name="'.$get.'['.$k.']" value="'.$oneV.'">';
                    }
                } else {
                    $htmlInputs .= '<input type="hidden" name="'.$get.'" value="'.$v.'">';
                }
            }
        }

        return $htmlInputs;
    }
}
