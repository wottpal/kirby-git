<?php

return function() {
  static $git = null; 
  if (!$git) {
    $git = new GitHelper;
  }

  return $git;
};
