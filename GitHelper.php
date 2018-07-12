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


  /**
  * Initializes the Helper-Class by gathering plugin-options.
  */
  public function __construct() {
    $this->user = kirby()->user()->name();
    $this->path = option('wottpal.git.path');
    $this->branch = option('wottpal.git.branch');
    $this->shouldPull = option('wottpal.git.shouldPull');
    $this->shouldPush = option('wottpal.git.shouldPush');
    $this->shouldCommit = option('wottpal.git.shouldCommit');
    $this->userHooks = option('wottpal.git.userHooks');
    $this->gitBin = option('wottpal.git.gitBin');
    $this->windowsMode = option('wottpal.git.windowsMode');
    $this->debug = option('wottpal.git.debug');
  }


  /**
  * Initializes the Git.php repository object.
  */
  private function initRepo()
  {
    if ($this->repo) return true;

    if (!class_exists("Git")) {
      if (file_exists(__DIR__ . DS . 'Git.php' . DS. 'Git.php')) {
        require __DIR__ . DS . 'Git.php' . DS. 'Git.php';
      }
    }

    if (!class_exists("Git")) {
      throw new Exception('Git class not found. Is the Git.php submodule installed?');
      // die('Git class not found. Is the Git.php submodule installed?');
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
  private function getRepo() {
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
    return $result !== '';
  }


  /**
  * Called from a Kirby-hook when content got changed.
  */
  public function changeHandler($message)
  {
    try {
      $this->initRepo();

      if ($this->branch) $this->getRepo()->checkout($this->branch);
      if ($this->shouldPull) $this->pull();
      if ($this->shouldCommit && $this->hasChangesToCommit()) $this->commit("{$message}\nBy: {$this->user}");
      if ($this->shouldPush) $this->push();

      if ($this->debug && !$this->hasChangesToCommit()) f::write(kirby()->roots()->index() .'/log-git-' . time() . '.txt', 'Hook fired but no changes to commit.');

    } catch(Exception $exception) {
      $errorMessage = 'Unable to update git: ' . $exception->getMessage();

      if ($this->debug) f::write(kirby()->roots()->index() .'/log-git-' . time() . '.txt', $errorMessage);
      throw new Exception($errorMessage);
    }
  }
}
