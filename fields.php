<?php


return [

  'gitlog' => [
    'computed' => [
      'gitLog' => function () use ($gitHelper) {
        $log = $gitHelper->getRepo()->log('{%n \"commit\": \"%H\",%n \"date\": \"%at\",%n \"message\": \"%s\",%n \"author\": \"%an\"%n},');
        $log = rtrim($log,",");
        $log = "[{$log}]";

        return json_decode($log);
      }
    ]
  ],

  'gitRevisions' => [
    'computed' => [
      'gitRevisions' => function () use ($gitHelper) {
        $log = $gitHelper->getRepo()->log('{%n \"commit\": \"%H\",%n \"date\": \"%at\",%n \"message\": \"%s\",%n \"author\": \"%an\"%n},');
        $log = rtrim($log,",");
        $log = "[{$log}]";

        return json_decode($log);
      }
    ]
  ],

];
