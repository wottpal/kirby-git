# Kirby Git

![MIT](https://img.shields.io/badge/Kirby-3-green.svg)
[![MIT](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/wottpal/kirby-anchor-headings/master/LICENSE)

This is a proof-of-concept of adding automatic version control via hooks to Kirby 3. It's based on [this](https://github.com/blankogmbh/kirby-git-commit-and-push-content) Kirby 2 plugin by @pascalmh and @blankogmbh.


## Usage

Just put it in your `/site/plugins` folder. If you install it as a submodule or just clone it from Github don't forget to initialize it's submodules `git
submodule update --init --recursive`.


## Options

```php
'options' => [
  'path' => kirby()->roots(),  // or just kirby()->roots()->content()
  'branch' => 'master',
  'shouldPull' => false,
  'shouldPush' => false,
  'shouldCommit' => true,
  'userHooks' => false,
  'gitBin' => '',
  'windowsMode' => false,
  'debug' => true,
]
```
