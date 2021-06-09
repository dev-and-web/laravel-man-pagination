<?php

namespace LaravelManPagination\Config;

/**
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
abstract class SingletonConfig
{
    /**
     * Singleton
     *
     * @return mixed
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * SingletonConfig constructor.
     * private - because is not allowed to be called from outside
     */
    private function __construct()
    {

    }

    /**
     * private - prevent the occurrence from being cloned
     */
    private function __clone()
    {

    }
}
