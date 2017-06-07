<?php
/**
 * @author Donii Sergii <doniysa@gmail.com>
 */

$loader = require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$loader->setPsr4('sonrac\\debug\\tests\\', __DIR__);

config(null, null, [
    'configPath' => __DIR__ . '/../config',
]);
env(null, null, [
    'configFile' => __DIR__ . '/data/env.json'
]);