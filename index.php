<?php

require_once __DIR__ . DS . 'GitHelper.php';

Kirby::plugin('wottpal/git', [

  'options' => [
    'dir' => 'index', // or e.g. 'content'
    'branch' => 'master',
    'shouldPull' => false,
    'shouldPush' => false,
    'shouldCommit' => false,
    'userHooks' => false,
    'gitBin' => '',
    'windowsMode' => false,
    'debug' => false,
    'logFile' => 'git-log.txt'
  ],

  'hooks' => require_once __DIR__ . DS . 'hooks.php',

  'fields' => require_once __DIR__ . DS . 'fields.php',

  'siteMethods' => [
    'git' => require_once __DIR__ . DS . 'src' . DS . 'siteMethods/git.php'
  ]

]);
