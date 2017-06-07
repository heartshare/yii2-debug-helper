# Configurable debug component for yii2

First see package 

https://github.com/sonrac/yii2-environment-config-formatter

Init config will be included with this package.

Usages:

As component: 

add to components section your application:

```php
'deb' => [
    'class' => 'sonrac\debug\components\Debug'
],
```

and use debug info:

var_dump:
```php
Yii::$app->deb->dm($a);
```

var_dump & exit:
```php
Yii::$app->deb->dm($a, true);
```

print_r:

```php
Yii::$app->deb->pr($a);
```

print_r & exit:

```php
Yii::$app->deb->pr($a, true);
```

echo:

```php
Yii::$app->deb->str($a);
```

echo & exit:

```php
Yii::$app->deb->str($a, true);
```

If last argument true, application will exiting.

## Component options:

| option | type | description |
| ------ | ---- | ----------- |
| checkEnvironmentSettings | bool | if true, check config from env |
| checkDeveloperWithIp | bool | Check developer permission with ip address |
| enableKint | bool | Enable kint for debug info on var_dump |
| KintTheme | string | Kint theme |
| exit | bool | Exit always after show debug info |
| ips | array | Allowed ips list |
| traceLevel | int | Max call level in stack trace (if you are not using kint) |
| cookieName | string | Developer cookie name. If enabled, ignored all options if cookie detected |
| useFormat | string | format for debug info show |
| format | string | message format |

## Methods

* `enableKint` - Force enable kint theme

* `checkKint` - Check enabled Kint theme

* `getDir` - Get dir $default if user are developer or $alternate otherwise
    - string `default`
    - string `alternate`
    
* `getName` - Get script suffix $alternate if not developer of $default otherwise

* `isDeveloperMode` - Check developer mode

* `exp` - Print message

* `trace` - Show trace

* `isDeveloper` - Check developer

* `checkCookieDeveloper` - Check existing developer cookie

* `str` - Print message

* `getExitVal` - check exiting value in arguments

* `getArgs` - Get function args

* `printMessage` - Print message

* `getEOL` - Get End-Of-Line characters (<br/> or PHP_EOL in console)

* `pr` - print_r

* `dm` - var_dump

* `preBegin` - show begin pre tag

* `preEnd` - show end pre tag

* `shortTrace` - show short trace

* `isCheckEnvironmentSettings` - check environment setting return checking

* `setCheckEnvironmentSettings` - Set use env settings for check developer mode

* `getCheckDeveloperWithIp` - get check developer with ip 

* `setCheckDeveloperWithIp` - set check developer with ip value

* `isEnableKint` - Check kint enabled option

* `setEnableKint` - Set kint enabled

* `getKintTheme` - Get kint theme

* `setKintTheme` - Set kint theme

* `isExit` - Check exit option

* `setExit` - Set exit option

* `getIps` - Get allow ips list

* `setIps` - Set allow ips list

* `getCookieCheck` - Get cookie check option value

* `setCookieCheck` - Set cookie check option value

* `setUseFormat` - Set use message format option value

* `getUseFormat` - Get use format option value

* `setFormat` - Set messages format

* `getFormat` - Get messages format