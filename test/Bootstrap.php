<?php

namespace GithubWebhooksTest;

use \RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

/**
 * Class Bootstrap
 * @package GithubWebhooksTest
 */
class Bootstrap
{
    /**
     * @var array
     */
    protected static $config;

    public static function init()
    {
        static::loadConfig();
        static::initAutoloader();
    }

    /**
     * @return array
     */
    public static function getConfig()
    {
        return static::$config;
    }

    /**
     * @throws \RuntimeException
     */
    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (is_readable($vendorPath . '/autoload.php')) {
            include $vendorPath . '/autoload.php';
        } else {
            throw new RuntimeException("Unable to load autoload.php. Run `php composer.phar install`.");
        }
    }

    /**
     * @param string $path
     * @return bool|string
     */
    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }

    protected static function loadConfig()
    {
        if (is_readable(__DIR__ . '/testConfig.php')) {
            self::$config = include __DIR__ . '/testConfig.php';
        } else {
            self::$config = include __DIR__ . '/testConfig.php.dist';
        }

        if (!is_array(self::$config)) {
            throw new RuntimeException("Expecting array as configuration ".gettype(self::$config)." given.");
        }
    }
}

Bootstrap::init();