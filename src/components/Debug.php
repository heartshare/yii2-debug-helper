<?php
/**
 * Copyright (c) 2016-2016.
 * OOO "Биржевой косалтинг" company.
 *
 * @author Doniy Serhey <doniysa@gmail.com>
 */

/**
 *
 *
 * @author Doniy Serhey <doniysa@gmail.com>
 */

namespace sonrac\debug\components;

use sonrac\debug\Debug as StaticDebug;
use sonrac\debug\IDebug;
use yii\base\Component;
use yii\base\ErrorException;

/**
 * Class Debug
 * Debug helper
 *
 * @property bool   $checkEnvironmentSettings
 * @property bool   $checkDeveloperWithIp
 * @property string $cookieName
 * @property string $KintTheme
 * @property bool   $cookieCheck
 * @property bool   $useFormat
 * @property array  $ips
 *
 * @package common\components
 * @author  Sergey Doniy <doniysa@gmail.com>
 */
class Debug extends Component implements IDebug
{

    /**
     * Check env settings for developer mode
     *
     * @var bool
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    protected $_checkEnvironmentSettings = true;

    /**
     * Check developer with ip settings or cookie set
     *
     * @var bool
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    protected $_checkDeveloperWithIp = true;

    /**
     * Enable/Disable Kint component on dump
     *
     * @var bool
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    public $enableKint = true;

    /**
     * Kint theme
     *
     * @var string
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    public $_KintTheme = 'original';

    /**
     * Exit if true after print
     *
     * @var bool
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    public $exit = false;

    /**
     * Developers ip list
     *
     * @var array
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    public $_ips = [
        '127.0.0.1',
        '195.138.79.99',
        '192.168.120.52',
    ];
    /**
     * Trace level
     *
     * @var int
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    public $traceLevel = 8;
    /**
     * Message print format
     *
     * @var string
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    protected $_format = "[date time]: new_line";
    /**
     * Developer cookie name
     *
     * @var string
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    protected $_cookieName = null;
    /**
     * Check developer cookie
     *
     * @var bool
     *
     * @author Serhey Doniy <doniysa@gmail.com>7
     */
    protected $_cookieCheck = true;
    
    /**
     * Use print format message
     *
     * @var bool
     *
     * @author Serhey Doniy <doniysa@gmail.com>
     */
    protected $_useFormat = true;

    /**
     * @inheritdoc
     */
    public function __construct(array $config = null)
    {
        parent::__construct($config);

        if (!$this->_cookieName) {
            $nameEnv = \sonrac\ConfigFormatter\env('DEVELOPER_COOKIE_NAME');
            $this->_cookieName = mb_strlen($nameEnv) > 10 ? $nameEnv : self::COOKIE_DEVELOPER;
        }

        if ($instance = StaticDebug::getInstance(false)) {
            $properties = [
                'checkDeveloperWithIp', 'checkEnvironmentSettings', 'useFormat', 'ips', 'exit',
                'traceLevel', 'format', 'cookieName', 'cookieCheck',
            ];

            foreach ($properties as $property) {
                $this->{$property} = $instance->{$property};
            }
        }
    }

    /**
     * Get dir if using two folder for js and css, first - origin (by default), second - min (by default)
     *
     * @param string $default
     * @param string $alternate
     *
     * @return string
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function getDir($default = 'origin/', $alternate = 'min/')
    {
        return $this->isDeveloper() ? $default : $alternate;
    }

    /**
     * Get filename
     *
     * @param string $alternate
     * @param string $default
     *
     * @return string
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function getName($alternate = 'min.', $default = '')
    {
        return $this->isDeveloper() ? $default : $alternate;
    }

    public function isDeveloperMode()
    {
        return $this->isDeveloper();
    }

    /**
     * Print message
     *
     * @param $message
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function exp($message)
    {
        $this->trace();

        $this->str($message);
    }

    /**
     * Show trace
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function trace()
    {
        if ($this->isDeveloper()) {
            if ($this->checkKint()) {
                \Kint::trace();
            } else {
                $this->shortTrace();
            }
        }
    }

    /**
     * Check kint enable and set kint theme
     *
     * @return bool
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function checkKint()
    {
        if ($this->enableKint) {
            \Kint::enabled(true);
            \Kint::$theme = $this->_KintTheme ?: 'solarized-dark';

            return true;
        }

        return false;
    }

    /**
     * Check developer mode
     *
     * @return bool
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function isDeveloper()
    {

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $enabled = in_array($ip, $this->_ips, null);

        if ($this->_checkEnvironmentSettings) {
            if (env('USE_DEVELOPER_MODE')) {
                $enabled = ($this->_checkDeveloperWithIp ? $enabled : true) && true;
            } else {
                $enabled = $this->_checkDeveloperWithIp ? $enabled : true;
            }
        }

        return $enabled || $this->checkCookieDeveloper();
    }

    /**
     * Check exists cookie developer
     *
     * @return bool
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function checkCookieDeveloper()
    {
        return $this->_cookieCheck && array_key_exists($this->_cookieName, $_COOKIE);
    }

    /**
     * Print message
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function str()
    {
        if ($this->isDeveloper()) {
            $args = func_get_args();
            $exit = $this->getExitVal($args);
            $args = $this->getArgs($args, $exit);

            $args = is_array($args) ? $args : [$args];

            $this->printMessage($args);

            $this->ex(0, $exit);
        }
    }

    /**
     * Check exist exit argument
     *
     * @param $args
     *
     * @return null
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function getExitVal($args)
    {
        $length = count($args);

        return array_key_exists($length - 1, $args) && is_bool($args[$length - 1]) ? $args[$length - 1] : null;
    }

    /**
     * Get arguments list
     *
     * @param $args
     * @param $exit
     *
     * @return mixed
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function getArgs($args, $exit)
    {
        $length = count($args);
        if ($exit && $length) {
            unset($args[$length - 1]);
        }

        return $args;
    }

    /**
     * Print message
     *
     * @param array       $args
     * @param string|null $func
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function printMessage($args, $func = null)
    {

        $time = time();
        $messageBegin = str_replace([
            "date",
            "time",
            "new_line",
        ], [
            date("Y-m-d", $time),
            date("H:i:s", $time),
            $this->getEOL(),
        ], $this->_format);
        if ($this->_useFormat) {
            echo $this->getEOL() . $messageBegin . $this->getEOL();
        }

        if ($func) {
            try {
                call_user_func_array($func, $args);
            } catch (ErrorException $exp) {
                var_dump($args);
                exit;
            }
        } else {
            foreach ($args
                     as
                     $_next) {
                echo $this->getEOL() . $_next . $this->getEOL();
            }
        }

    }

    /**
     * Get system end of line or '<br/>' for web
     *
     * @return string
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function getEOL()
    {
        if (isset($_SERVER['REMOTE_ADDR']) && $this->isDeveloper()) {
            return "<br/>";
        } else {
            return PHP_EOL;
        }
    }

    /**
     * Stop script executing
     *
     * @param int  $code
     * @param bool $exit
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function ex($code = 0, $exit = true)
    {
        if ($this->isDeveloper() && $exit) {
            exit;
        }
    }

    /**
     * Print_r arguments
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function pr()
    {
        if ($this->isDeveloper()) {
            $args = func_get_args();
            $exit = $this->getExitVal($args);
            $args = $this->getArgs($args, $exit);
            foreach ($args as $arg) {
                echo '<pre>';
                $this->printMessage([$arg], 'print_r');
                echo '</pre>';
                $this->shortTrace();
            }

            $this->ex(0, $exit);
        }
    }

    /**
     * Print begin <pre>
     *
     * @return string
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function preBegin()
    {
        if ($this->isDeveloper()) {
            return $this->preShow();
        }
    }


    /**
     * Show begin or end '<pre>'
     *
     * @param bool $end
     *
     * @return string
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    protected function preShow($end = false)
    {
        if (extension_loaded('xdebug')) {
            return "";
        }

        return $end ? '</pre>' : '<pre>';
    }

    /**
     * Show end </pre>
     *
     * @return string
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function preEnd()
    {
        if ($this->isDeveloper()) {
            return $this->preShow(true);
        }
    }

    /**
     * Var_dump for arguments
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function dm()
    {
        if ($this->isDeveloper()) {
            $args = func_get_args();
            $exit = $this->getExitVal($args);
            echo $this->preBegin();
            if (count($this->getArgs($args, $exit))) {
                $args = $this->getArgs($args, $exit);
                $args = is_array($args) ? $args : [$args];
                if ($this->checkKint()) {
                    \Kint::dump($args);
                } else {
                    $this->printMessage($args, 'var_dump');
                    $this->shortTrace();
                }
            }
            echo $this->preEnd();
            $this->ex(0, $exit);
        }
    }

    /**
     * Short trace script executing
     *
     * @param int $level
     * @param int $begin
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function shortTrace($level = 8, $begin = 0)
    {
        if ($this->traceLevel === false) {
            return;
        }
        $level = $this->traceLevel ?: $level;
        $trace = debug_backtrace();

        for ($i = $begin; $i < $level + $begin; $i++) {
            if (!isset($trace[$i]))
                break;

            $this->oneTrace($trace[$i]);
        }

    }

    /**
     * Out one trace message
     *
     * @param $nextTrace
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    protected function oneTrace($nextTrace)
    {
        if (!isset($nextTrace['file'])) {
            $nextTrace['file'] = $nextTrace['class'];
            $nextTrace['line'] = '?';
        }
        echo "<span class='debug-trace-deb' style='padding: 2px; margin:1px; border: 1px dashed gray; display:block;font-size: 14px; background-color: #E7E7E7; color: black;'>Called in {$nextTrace['file']} on line {$nextTrace['line']} in function {$nextTrace['function']}</span>";
    }

    /**
     * @return boolean
     */
    public function getCheckEnvironmentSettings(): bool
    {
        return $this->_checkEnvironmentSettings;
    }

    /**
     * @param boolean $checkEnvironmentSettings
     */
    public function setCheckEnvironmentSettings(bool $checkEnvironmentSettings)
    {
        $this->_checkEnvironmentSettings = $checkEnvironmentSettings;
    }

    /**
     * @return boolean
     */
    public function getCheckDeveloperWithIp(): bool
    {
        return $this->_checkDeveloperWithIp;
    }

    /**
     * @param boolean $checkDeveloperWithIp
     */
    public function setCheckDeveloperWithIp(bool $checkDeveloperWithIp)
    {
        $this->_checkDeveloperWithIp = $checkDeveloperWithIp;
    }

    /**
     * @return boolean
     */
    public function isEnableKint(): bool
    {
        return $this->enableKint;
    }

    /**
     * @param boolean $enableKint
     */
    public function setEnableKint(bool $enableKint)
    {
        $this->enableKint = $enableKint;
    }

    /**
     * @return string
     */
    public function getKintTheme(): string
    {
        return $this->_KintTheme;
    }

    /**
     * @param string $KintTheme
     */
    public function setKintTheme(string $KintTheme)
    {
        $this->_KintTheme = $KintTheme;
    }

    /**
     * @return boolean
     */
    public function isExit(): bool
    {
        return $this->exit;
    }

    /**
     * @param boolean $exit
     */
    public function setExit(bool $exit)
    {
        $this->exit = $exit;
    }

    /**
     * @return array
     */
    public function getIps(): array
    {
        return $this->_ips;
    }

    /**
     * @param array $ips
     */
    public function setIps(array $ips)
    {
        $this->_ips = $ips;
    }

    /**
     * @return int
     */
    public function getTraceLevel(): int
    {
        return $this->traceLevel;
    }

    /**
     * @param int $traceLevel
     */
    public function setTraceLevel(int $traceLevel)
    {
        $this->traceLevel = $traceLevel;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->_format;
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format)
    {
        $this->_format = $format;
    }

    /**
     * @return string
     */
    public function getCookieName(): string
    {
        return $this->_cookieName;
    }

    /**
     * @param string $cookieName
     */
    public function setCookieName(string $cookieName)
    {
        $this->_cookieName = $cookieName;
    }

    /**
     * @return boolean
     */
    public function getCookieCheck(): bool
    {
        return $this->_cookieCheck;
    }

    /**
     * @param boolean $cookieCheck
     */
    public function setCookieCheck(bool $cookieCheck)
    {
        $this->_cookieCheck = $cookieCheck;
    }

    /**
     * @return boolean
     */
    public function getUseFormat(): bool
    {
        return $this->_useFormat;
    }

    /**
     * @param boolean $useFormat
     */
    public function setUseFormat(bool $useFormat)
    {
        $this->_useFormat = $useFormat;
    }

    /**
     * Set developer cookie
     *
     * @param $value
     *
     * @author Doniy Sergey <doniysa@gmail.com>
     */
    public function setCookie($value)
    {
        if ($this->_cookieCheck) {
            setcookie(
                $this->_cookieName,
                $value,
                time() + (10 * 365 * 24 * 60 * 60)
            );
        }
    }
}