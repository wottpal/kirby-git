<?php


return [

  /**
  * Page-Hooks
  */
  'page.create:after' => function ($page) use ($gitHelper) {
    $gitHelper->changeHandler("Created Page: {$page->id()}");
  },
  'page.update:after' => function ($newPage,$oldPage) use ($gitHelper) {
    $gitHelper->changeHandler("Edited Page: {$newPage->id()}");
  },
  'page.delete:after' => function ($status, $page) use ($gitHelper) {
    $gitHelper->changeHandler("Deleted Page: {$page->id()}");
  },
  'page.changeNum:after' => function ($newPage,$oldPage) use ($gitHelper) {
    $oldNum = $oldPage->num() ?? "None";
    $gitHelper->changeHandler("Sorted Page: {$newPage->id()} ({$oldNum} → {$newPage->num()})");
  },
  'page.changeSlug:after' => function ($newPage,$oldPage) use ($gitHelper) {
    $oldSlug = $oldPage->slug() ?? "None";
    $gitHelper->changeHandler("Changed Slug: {$newPage->id()} ({$oldSlug} → {$newPage->slug()})");
  },
  'page.changeStatus:after' => function ($newPage,$oldPage) use ($gitHelper) {
    $oldStatus = $oldPage->status() ?? "None";
    $gitHelper->changeHandler("Changed Status: {$newPage->id()} ({$oldStatus} → {$newPage->status()})");
  },
  'page.changeTemplate:after' => function ($newPage,$oldPage) use ($gitHelper) {
    $oldTemplate = $oldPage->template() ?? "None";
    $gitHelper->changeHandler("Changed Template: {$newPage->id()} ({$oldTemplate} → {$newPage->template()})");
  },
  'page.changeTitle:after' => function ($newPage,$oldPage) use ($gitHelper) {
    $oldTitle = $oldPage->title() ?? "None";
    $gitHelper->changeHandler("Changed Title: {$newPage->id()} ({$oldTitle} → {$newPage->title()})");
  },

  /**
  * File-Hooks
  */
  'file.create:after' => function ($file) use ($gitHelper) {
    $gitHelper->changeHandler("Uploaded File: {$file->id()}");
  },
  'file.delete:after' => function ($status, $file) use ($gitHelper) {
    $gitHelper->changeHandler("Deleted File: {$file->id()}");
  },
  'file.changeName:after' => function ($newFile,$oldFile) use ($gitHelper) {
    $oldFilename = $oldFile->filename() ?? "None";
    $gitHelper->changeHandler("Changed Filename: {$newFile->id()} ({$oldFilename} → {$newFile->filename()})");
  },
  'file.changeSort:after' => function ($newFile,$oldFile) use ($gitHelper) {
    $oldSort = $oldFile->sort() ?? "None";
    $gitHelper->changeHandler("Sorted File: {$newFile->id()} ({$oldSort} → {$newFile->sort()})");
  },
  'file.update:after' => function ($newFile,$oldFile) use ($gitHelper) {
    $gitHelper->changeHandler("Edited File-Metadata: {$newFile->id()}");
  },
  'file.replace:after' => function ($newFile,$oldFile) use ($gitHelper) {
    $gitHelper->changeHandler("Replaced File: {$newFile->id()}");
  },

  /**
  * Site-Hooks
  */
  'site.update:after' => function ($newSite,$oldSite) use ($gitHelper) {
    $gitHelper->changeHandler("Edited Site");
  },

  /**
  * Avatar-Hooks
  */
  'avatar.create:after' => function ($avatar) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $avatar->user()->name() ?? $avatar->user()->email();
    $gitHelper->changeHandler("Uploaded Avatar: {$name}");
  },
  'avatar.replace:after' => function ($newAvatar, $oldAvatar) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $newAvatar->user()->name() ?? $newAvatar->user()->email();
    $gitHelper->changeHandler("Replaced Avatar: {$name}");
  },
  'avatar.delete:after' => function ($status, $avatar) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $avatar->user()->name() ?? $avatar->user()->email();
    $gitHelper->changeHandler("Deleted Avatar: {$name}");
  },

  /**
  * User-Hooks
  */
  'user.changeEmail:after' => function ($newUser, $oldUser) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldEmail = $oldUser->email() ?? "None";
    $gitHelper->changeHandler("Changed E-Mail: {$name} ({$oldEmail} → {$newUser->email()})");
  },
  'user.changeName:after' => function ($newUser, $oldUser) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldName = $oldUser->name() ?? "None";
    $newName = $newUser->name() ?? "None";
    $gitHelper->changeHandler("Changed Name: {$name} ({$oldName} → {$newName})");
  },
  'user.changeLanguage:after' => function ($newUser, $oldUser) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldLang = $oldUser->language() ?? "None";
    $gitHelper->changeHandler("Changed Language: {$name} ({$oldLang} → {$newUser->language()})");
  },
  'user.changePassword:after' => function ($newUser, $oldUser) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $gitHelper->changeHandler("Changed Password: {$name}");
  },
  'user.changeRole:after' => function ($newUser, $oldUser) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldRole = $oldUser->role() ?? "None";
    $gitHelper->changeHandler("Changed Role: {$name} ({$oldRole} → {$newUser->role()})");
  },
  'user.create:after' => function ($user) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $user->name() ?? $user->email();
    $gitHelper->changeHandler("Created User: {$name}");
  },
  'user.update:after' => function ($newUser,$oldUser) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $gitHelper->changeHandler("Edited User: {$name}");
  },
  'user.delete:after' => function ($status, $user) use ($gitHelper) {
    if (!$gitHelper->userHooksEnabled()) return;
    $name = $user->name() ?? $user->email();
    $gitHelper->changeHandler("Deleted User: {$name}");
  },

];
