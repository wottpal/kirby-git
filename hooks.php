<?php


return [

  /**
  * Page-Hooks
  */
  'page.create:after' => function ($page) {
    site()->git()->changeHandler("Created Page: {$page->id()}");
  },
  'page.update:after' => function ($newPage,$oldPage) {
    site()->git()->changeHandler("Edited Page: {$newPage->id()}");
  },
  'page.delete:after' => function ($status, $page) {
    site()->git()->changeHandler("Deleted Page: {$page->id()}");
  },
  'page.changeNum:after' => function ($newPage,$oldPage) {
    $oldNum = $oldPage->num() ?? "None";
    site()->git()->changeHandler("Sorted Page: {$newPage->id()} ({$oldNum} → {$newPage->num()})");
  },
  'page.changeSlug:after' => function ($newPage,$oldPage) {
    $oldSlug = $oldPage->slug() ?? "None";
    site()->git()->changeHandler("Changed Slug: {$newPage->id()} ({$oldSlug} → {$newPage->slug()})");
  },
  'page.changeStatus:after' => function ($newPage,$oldPage) {
    $oldStatus = $oldPage->status() ?? "None";
    site()->git()->changeHandler("Changed Status: {$newPage->id()} ({$oldStatus} → {$newPage->status()})");
  },
  'page.changeTemplate:after' => function ($newPage,$oldPage) {
    $oldTemplate = $oldPage->template() ?? "None";
    site()->git()->changeHandler("Changed Template: {$newPage->id()} ({$oldTemplate} → {$newPage->template()})");
  },
  'page.changeTitle:after' => function ($newPage,$oldPage) {
    $oldTitle = $oldPage->title() ?? "None";
    site()->git()->changeHandler("Changed Title: {$newPage->id()} ({$oldTitle} → {$newPage->title()})");
  },

  /**
  * File-Hooks
  */
  'file.create:after' => function ($file) {
    site()->git()->changeHandler("Uploaded File: {$file->id()}");
  },
  'file.delete:after' => function ($status, $file) {
    site()->git()->changeHandler("Deleted File: {$file->id()}");
  },
  'file.changeName:after' => function ($newFile,$oldFile) {
    $oldFilename = $oldFile->filename() ?? "None";
    site()->git()->changeHandler("Changed Filename: {$newFile->id()} ({$oldFilename} → {$newFile->filename()})");
  },
  'file.changeSort:after' => function ($newFile,$oldFile) {
    $oldSort = $oldFile->sort() ?? "None";
    site()->git()->changeHandler("Sorted File: {$newFile->id()} ({$oldSort} → {$newFile->sort()})");
  },
  'file.update:after' => function ($newFile,$oldFile) {
    site()->git()->changeHandler("Edited File-Metadata: {$newFile->id()}");
  },
  'file.replace:after' => function ($newFile,$oldFile) {
    site()->git()->changeHandler("Replaced File: {$newFile->id()}");
  },

  /**
  * Site-Hooks
  */
  'site.update:after' => function ($newSite,$oldSite) {
    site()->git()->changeHandler("Edited Site");
  },

  /**
  * Avatar-Hooks
  */
  'avatar.create:after' => function ($avatar) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $avatar->user()->name() ?? $avatar->user()->email();
    site()->git()->changeHandler("Uploaded Avatar: {$name}");
  },
  'avatar.replace:after' => function ($newAvatar, $oldAvatar) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $newAvatar->user()->name() ?? $newAvatar->user()->email();
    site()->git()->changeHandler("Replaced Avatar: {$name}");
  },
  'avatar.delete:after' => function ($status, $avatar) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $avatar->user()->name() ?? $avatar->user()->email();
    site()->git()->changeHandler("Deleted Avatar: {$name}");
  },

  /**
  * User-Hooks
  */
  'user.changeEmail:after' => function ($newUser, $oldUser) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldEmail = $oldUser->email() ?? "None";
    site()->git()->changeHandler("Changed E-Mail: {$name} ({$oldEmail} → {$newUser->email()})");
  },
  'user.changeName:after' => function ($newUser, $oldUser) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldName = $oldUser->name() ?? "None";
    $newName = $newUser->name() ?? "None";
    site()->git()->changeHandler("Changed Name: {$name} ({$oldName} → {$newName})");
  },
  'user.changeLanguage:after' => function ($newUser, $oldUser) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldLang = $oldUser->language() ?? "None";
    site()->git()->changeHandler("Changed Language: {$name} ({$oldLang} → {$newUser->language()})");
  },
  'user.changePassword:after' => function ($newUser, $oldUser) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    site()->git()->changeHandler("Changed Password: {$name}");
  },
  'user.changeRole:after' => function ($newUser, $oldUser) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    $oldRole = $oldUser->role() ?? "None";
    site()->git()->changeHandler("Changed Role: {$name} ({$oldRole} → {$newUser->role()})");
  },
  'user.create:after' => function ($user) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $user->name() ?? $user->email();
    site()->git()->changeHandler("Created User: {$name}");
  },
  'user.update:after' => function ($newUser,$oldUser) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $newUser->name() ?? $newUser->email();
    site()->git()->changeHandler("Edited User: {$name}");
  },
  'user.delete:after' => function ($status, $user ) {
    if (!site()->git()->userHooksEnabled()) return;
    $name = $user->name() ?? $user->email();
    site()->git()->changeHandler("Deleted User: {$name}");
  },

];
