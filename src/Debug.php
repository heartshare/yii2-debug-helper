<?php
/**
 * Copyright (c) 2016-2016.
 * OOO "Биржевой косалтинг" company.
 *
 * @author Doniy Serhey <doniysa@gmail.com>
 */

/**
 * @author Doniy Serhey <doniysa@gmail.com>
 */

namespace sonrac\debug;

use sonrac\debug\components\Debug as DebugComponent;
use sonrac\ConfigFormatter;
use yii;

/**
 * Class Debug
 *
 * @method static |string  getDir($default = 'origin/', $alternate = 'min/')
 * @method static |bool    enableKint()
 * @method static |bool    exit()
 * @method static |integer traceLevel()
 * @method static |string  getKintTheme()
 * @method static |string  getFormat()
 * @method static |string  getCookieName()
 * @method static |bool    getCookieCheck()
 * @method static |bool    getCheckDeveloperWithIp()
 * @method static |bool    getCheckEnvironmentSettings ()
 * @method static |bool    getUseFormat()
 * @method static |string  getName($alternate = 'min.', $default = '')
 * @method static |string  exp($message)
 * @method static |string  trace()
 * @method static |string  checkKint()
 * @method static |bool    isDeveloper()
 * @method static |bool    isDeveloperMode()
 * @method static |string  checkCookieDeveloper()
 * @method static |string  str()
 * @method static |string  getEOL()
 * @method static |string  ex($code = 0, $exit = true)
 * @method static |string  pr($arg1 = null, $arg2= null, $arg3 = null)
 * @method static |string  preBegin()
 * @method static |string  preEnd()
 * @method static |string  dm($arg1 = null, $arg2= null, $arg3 = null)
 * @method static |string  shortTrace($level = 8, $begin = 0)
 * @method static |string  setCookie($value)
 *
 * @package common\helpers
 * @author  Sergey Doniy <doniysa@gmail.com>
 */
class Debug implements IDebug
{
    /** @var  DebugComponent */
    protected static $_component;

    /**
     * @inheritdoc
     */
    public static function __callStatic($name, $arguments)
    {
        static::getInstance();

        if (property_exists(static::$_component, $name)) {
            return static::$_component->{$name};
        }

        return call_user_func_array([static::$_component, $name], $arguments);
    }

    /**
     * Get debug instance
     *
     * @param bool $init
     *
     * @return DebugComponent
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public static function getInstance($init = true)
    {
        if (!static::$_component && $init) {
            $config = config('deb');
            unset($config['class']);
            static::$_component = Yii::$app ? Yii::$app->deb : new DebugComponent($config);
        }

        return static::$_component;
    }

    /**
     * Get developer ips
     *
     * @return array
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public static function getIps()
    {
        static::getInstance();

        return static::$_component->ips;
    }

    /**
     * Set developers ips
     *
     * @param array $ips
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public static function setIps(array $ips)
    {
        static::getInstance()->ips = $ips;
    }

    /**
     * Add developer ip
     *
     * @param $ip
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public static function addIp($ip)
    {
        static::getInstance()->ips[] = $ip;
    }
}