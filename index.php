<?php

require_once __DIR__ . DS . 'GitHelper.php';
$gitHelper = new GitHelper();


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

]);
