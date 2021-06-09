<?php

namespace LaravelManPagination\Config;

use LaravelManPagination\Contracts\Config\ConfigInterface;

/**
 * @link     https://github.com/dev-and-web/laravel-man-pagination
 * @author   Stephen Damian <contact@devandweb.fr>
 * @license  MIT License
 */
class Config extends SingletonConfig implements ConfigInterface
{
    /**
     * @var Config
     */
    protected static ?self $instance = null;
    
    /**
     * @var array
     */
    private static array $config = [];
    
    /**
     * @var array
     */
    private static array $defaultConfig = [];

    /**
     * @param array $config
     */
    public static function set(array $config): void
    {
        self::$config = array_merge(self::$defaultConfig, $config);
    }

    /**
     * @return array|string
     */
    public static function get(string $param = null)
    {
        if (self::$defaultConfig === []) {
            self::$defaultConfig = require_once base_path().'/vendor/dev-and-web/laravel-man-pagination/src/default-config/config.php';
        }

        if (self::$config === []) {
            self::$config = self::$defaultConfig;
        }

        return self::$config[$param] ?? self::$config;
    }
}
