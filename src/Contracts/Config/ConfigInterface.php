<?php

namespace LaravelManPagination\Contracts\Config;

/**
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
Interface ConfigInterface
{
    public static function set(array $config): void;

    public static function get();
}
