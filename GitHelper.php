<?php

class GitHelper {

  public $path;
  public $branch;
  public $shouldPull;
  public $shouldPush;
  public $shouldCommit;
  public $userHooks;
  public $gitBin;
  public $windowsMode;
  public $debug;

  private $user;
  private $repo;
  private $isInitialized;


  /**
  * Initializes the Helper-Class by gathering plugin-options.
  */
  public function initOptions() {
    $this->isInitialized = True;

    $this->user = kirby()->user()->name() ?? kirby()->user()->email();;
    $dir = option('wottpal.git.dir');
    $this->path = kirby()->roots()->$dir();
    $this->branch = option('wottpal.git.branch');
    $this->shouldPull = option('wottpal.git.shouldPull');
    $this->shouldPush = option('wottpal.git.shouldPush');
    $this->shouldCommit = option('wottpal.git.shouldCommit');
    $this->userHooks = option('wottpal.git.userHooks');
    $this->gitBin = option('wottpal.git.gitBin');
    $this->windowsMode = option('wottpal.git.windowsMode');
    $this->debug = option('wottpal.git.debug');
    $this->logFile = kirby()->roots()->index() . DS . option('wottpal.git.logFile');
  }


  /**
  * Returns `true` if user-hooks are enabled in the config
  * IMPORTANT: It's a security risk to put sensible user-data under version-control.
  */
  public function userHooksEnabled() {
    if (!$this->isInitialized) $this->initOptions();
    return $this->userHooks;
  }


  /**
  * Returns the given path
  */
  public function path() {
    if (!$this->isInitialized) $this->initOptions();
    return $this->path;
  }


  /**
  * Initializes the Git.php repository object.
  */
  private function initRepo()
  {
    if ($this->repo) return true;
    if (!$this->isInitialized) $this->initOptions();

    if (!class_exists("Git")) {
      if (file_exists(__DIR__ . DS . 'Git.php' . DS. 'Git.php')) {
        require __DIR__ . DS . 'Git.php' . DS. 'Git.php';
      }
    }

    if (!class_exists("Git")) {
      throw new Exception('Git class not found. Is the Git.php submodule installed?');
    }

    if ($this->gitBin) {
      Git::set_bin($this->gitBin);
    }

    if ($this->windowsMode) {
      Git::windows_mode();
    }

    $this->repo = Git::open($this->path);

    if (!$this->repo->test_git()) {
      throw new Exception('System Git could not be found or is not working properly. ' . Git::get_bin());
      // trigger_error('git could not be found or is not working properly. ' . Git::get_bin());
    }
  }


  /**
  * Returns the Git.php repository.
  */
  public function getRepo() {
    if ($this->repo == null) $this->initRepo();

    return $this->repo;
  }


  /**
  * Commits to the repository.
  */
  public function commit($message) {
    $this->getRepo()->add('-A');
    $this->getRepo()->commit($message);
  }


  /**
  * Pushes to the remote repository.
  */
  public function push() {
    $this->getRepo()->push('origin', $this->branch);
  }


  /**
  * Pulls from the remote repository.
  */
  public function pull()
  {
    $this->getRepo()->pull('origin', $this->branch);
  }


  /**
  * Returns `true` if there are changes to commit.
  */
  public function hasChangesToCommit() {
    $result = $this->getRepo()->run('status --porcelain');
    // Didn't show new dirs
    // $result = $this->getRepo()->run('ls-files -m');
    return $result !== '';
  }


  /**
  * Called from a Kirby-hook when content got changed.
  */
  public function changeHandler($message)
  {
    if (!$this->isInitialized) $this->initOptions();

    try {
      if ($this->branch) $this->getRepo()->checkout($this->branch);
      if ($this->shouldPull) $this->pull();
      if ($this->shouldCommit && $this->hasChangesToCommit()) $this->commit("{$message}\nBy: {$this->user}", true);
      if ($this->shouldPush) $this->push();

      if (!$this->hasChangesToCommit() && $this->shouldCommit) $this->log('Hook fired but no changes');
      elseif ($this->shouldCommit) $this->log('Committed successfully');

    } catch(Exception $exception) {
      $errorMessage = 'Unable to update git: ' . $exception->getMessage();

      $this->log($errorMessage);
      // throw new Exception($errorMessage);
    }
  }


  /**
  * Creates a new log-message in the log-file.
  */
  private function log($message) {
    if (!$this->isInitialized) $this->initOptions();
    if (!$this->debug) return;

    $date = date("Y-m-d, H:i", time());
    $message = "[{$date}] {$message}\n";

    file_put_contents($this->logFile, $message, FILE_APPEND);
  }

}
