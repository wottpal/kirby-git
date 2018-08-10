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
        $parent = $this->model();
        $contentFile = $parent->contentFile();
        $contentFile = substr($contentFile, strlen(kirby()->root()) + 1);

        $commitFormat = '{%n \"commit\": \"%H\",%n \"date\" : \"%at\"%n},';
        $logCommand = 'log --follow --name-only --pretty=format:"' . $commitFormat . '" -- ' . $contentFile;
        $revisions = $gitHelper->getRepo()->run($logCommand);

        // Gather all former paths/filenames
        $matches = [];
        $pattern = '/\},\s*(.*)\s*/';
        preg_match_all($pattern, $revisions, $matches, PREG_SET_ORDER);
        $revisions = preg_replace($pattern, '},' , $revisions);

        // Format as valid Object
        $revisions = rtrim($revisions,",");
        $revisions = "[{$revisions}]";
        $revisions = json_decode($revisions, true);

        foreach($revisions as $idx => $revision) {
          // Map gathered names
          $formerContentFile = $matches[$idx][1];
          $revisions[$idx]['path'] = $formerContentFile;

          // Format date
          $date = $revision['date'];
          $revisions[$idx]['dateFormatted'] = date("Y-m-d, H:i", $date);

          // Gather content
          $revisionCommand = "show {$revision['commit']}:{$formerContentFile}";
          $revisionContent = $gitHelper->getRepo()->run($revisionCommand);
          $revisionContent = Kirby\Data\Yaml::decode($revisionContent);
          $revisions[$idx]['content'] = $revisionContent;

          // $revisions[$idx]['test'] = $parent->blueprint();
          // $revisions[$idx]['relative_path'] = $contentFile;


        }

        return $revisions;
      }
    ]
  ],

];
