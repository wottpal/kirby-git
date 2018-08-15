<?php

require_once __DIR__ . DS . 'GitHelper.php';
$gitHelper = new GitHelper();


Kirby::plugin('wottpal/git', [

  'options' => [
    'path' => kirby()->roots(),  // or kirby()->roots()->content()
    'branch' => 'master',
    'shouldPull' => false,
    'shouldPush' => false,
    'shouldCommit' => false,
    'userHooks' => false,
    'gitBin' => '',
    'windowsMode' => false,
    'debug' => false
  ],

  'hooks' => require_once __DIR__ . DS . 'hooks.php',

  'fields' => require_once __DIR__ . DS . 'fields.php',

]);
