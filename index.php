<?php

require(__DIR__ . DS . 'GitHelper.php');

$gitHelper = new GitHelper();


Kirby::plugin('wottpal/git', [

  'options' => [
    'path' => kirby()->roots(),  // or kirby()->roots()->content()
    'branch' => 'master',
    'shouldPull' => false,
    'shouldPush' => false,
    'shouldCommit' => true,
    'userHooks' => false,
    'gitBin' => '',
    'windowsMode' => false,
    'debug' => false
  ],

  'hooks' => [

    /**
    * Page-Hooks
    */
    'page.create:after' => function ($page) use ($gitHelper) {
      $gitHelper->changeHandler("New Page: {$page->id()}");
    },
    'page.update:after' => function ($newPage,$oldPage) use ($gitHelper) {
      $gitHelper->changeHandler("Updated Page: {$newPage->id()}");
    },
    'page.delete:after' => function ($status, $page) use ($gitHelper) {
      $gitHelper->changeHandler("Deleted Page: {$page->id()}");
    },
    'page.changeNum:after' => function ($newPage,$oldPage) use ($gitHelper) {
      $gitHelper->changeHandler("Sorted: {$newPage->id()} ({$oldPage->num()} → {$newPage->num()})");
    },
    'page.changeSlug:after' => function ($newPage,$oldPage) use ($gitHelper) {
      $gitHelper->changeHandler("Changed Slug: {$newPage->id()} ({$oldPage->slug()} → {$newPage->slug()})");
    },
    'page.changeStatus:after' => function ($newPage,$oldPage) use ($gitHelper) {
      $gitHelper->changeHandler("Changed Status: {$newPage->id()} ({$oldPage->status()} → {$newPage->status()})");
    },
    'page.changeTemplate:after' => function ($newPage,$oldPage) use ($gitHelper) {
      $gitHelper->changeHandler("Changed Template: {$newPage->id()} ({$oldPage->template()} → {$newPage->template()})");
    },
    'page.changeTitle:after' => function ($newPage,$oldPage) use ($gitHelper) {
      $gitHelper->changeHandler("Changed Title: {$newPage->id()} ({$oldPage->title()} → {$newPage->title()})");
    },

    /**
    * File-Hooks
    */
    'file.create:after' => function ($file) use ($gitHelper) {
      $gitHelper->changeHandler("New File: {$file->id()}");
    },
    'file.delete:after' => function ($status, $file) use ($gitHelper) {
      $gitHelper->changeHandler("Deleted File: {$file->id()}");
    },
    'file.changeName:after' => function ($newFile,$oldFile) use ($gitHelper) {
      $gitHelper->changeHandler("Changed Filename: {$newFile->id()} ({$oldFile->filename()} → {$newFile->filename()})");
    },
    'file.changeSort:after' => function ($newFile,$oldFile) use ($gitHelper) {
      $gitHelper->changeHandler("Sorted: {$newFile->id()} ({$oldFile->sort()} → {$newFile->sort()})");
    },
    'file.update:after' => function ($newFile,$oldFile) use ($gitHelper) {
      $gitHelper->changeHandler("Changed File Metadata: {$newFile->id()}");
    },
    'file.replace:after' => function ($newFile,$oldFile) use ($gitHelper) {
      $gitHelper->changeHandler("Replaced File: {$newFile->id()}");
    },

    /**
    * Site-Hooks
    */
    'site.update:after' => function ($newSite,$oldSite) use ($gitHelper) {
      $gitHelper->changeHandler("Updated Site");
    },

    /**
    * Avatar-Hooks
    */
    'avatar.create:after' => function ($avatar) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("New Avatar: {$avatar->user()->name()}");
    },
    'avatar.replace:after' => function ($newAvatar, $oldAvatar) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Replaced Avatar: {$newAvatar->user()->name()}");
    },
    'avatar.delete:after' => function ($status, $avatar) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Deleted Avatar: {$avatar->user()->name()}");
    },

    /**
    * User-Hooks
    */
    'user.changeEmail:after' => function ($newUser, $oldUser) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Changed E-Mail: {$newUser->name()} ({$oldUser->email()} → {$newUser->email()})");
    },
    'user.changeName:after' => function ($newUser, $oldUser) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Changed Name: {$newUser->name()} ({$oldUser->name()} → {$newUser->name()})");
    },
    'user.changeLanguage:after' => function ($newUser, $oldUser) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Changed Language: {$newUser->name()} ({$oldUser->language()} → {$newUser->language()})");
    },
    'user.changePassword:after' => function ($newUser, $oldUser) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Changed Password: {$newUser->name()}");
    },
    'user.changeRole:after' => function ($newUser, $oldUser) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Changed Role: {$newUser->name()} ({$oldUser->role()} → {$newUser->role()})");
    },
    'user.create:after' => function ($user) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("New User: {$user->name()}");
    },
    'user.update:after' => function ($newUser,$oldUser) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Updated User: {$newUser->name()}");
    },
    'user.delete:after' => function ($status, $user) use ($gitHelper) {
      if (!$gitHelper->userHooks) return;
      $gitHelper->changeHandler("Deleted User: {$user->name()}");
    },

  ],

  'fields' => [

    'gitlog' => [
      'computed' => [
        'gitLog' => function () use ($gitHelper) {
          $log = $gitHelper->getRepo()->log('{%n \"commit\": \"%H\",%n \"date\": \"%at\",%n \"message\": \"%s\",%n \"author\": \"%an\"%n},');
          $log = rtrim($log,",");
          $log = "[{$log}]";

          return json_decode($log);
        }
      ]
    ]

  ]


]);
