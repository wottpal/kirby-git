<?php


return [

  'gitLog' => [
    'computed' => [

      'gitLog' => function () use ($gitHelper) {
        // Gather all commits and format as valid JSOn
        $log = $gitHelper->getRepo()->log('{%n \"hash\": \"%h\",%n \"date\": \"%at\",%n \"message\": \"%s\",%n \"author\": \"%an\"},');
        $log = rtrim($log,",");
        $log = "[{$log}]";
        $log = json_decode($log, true);

        foreach($log as $idx => $commit) {

          // Format date
          $date = $commit['date'];
          $log[$idx]['dateFormatted'] = date("Y-m-d, H:i", $date);

        }

        return $log;
      }

    ]
  ],


  'gitRevisions' => [
    'computed' => [

      // 'blueprintFields' => function () {
      //   return $this->model()->blueprint();
      // },

      'gitRevisions' => function () use ($gitHelper) {
        $parent = $this->model();

        // Get the relative content file path
        $contentFile = $parent->contentFile();
        $contentFile = substr($contentFile, strlen(kirby()->root()) + 1);

        // Get all commits where the content file was modified
        $commitFormat = '{%n \"hash\": \"%h\",%n \"date\" : \"%at\"%n,%n \"message\": \"%s\",%n \"author\": \"%an\"},';
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

          // Get (former) template from filename
          $formerTemplate = basename(basename($formerContentFile, ".txt"), ".md");
          $revisions[$idx]['template'] = $formerTemplate;

          // Gather and decode content
          $revisionCommand = "show {$revision['hash']}:{$formerContentFile}";
          $revisionContent = $gitHelper->getRepo()->run($revisionCommand);
          $revisionContent = Kirby\Data\Txt::decode($revisionContent);
          $virtualPage = new Kirby\Cms\Page([
             'template' => $formerTemplate,
             'content' => $revisionContent,
             'slug' => 'totally-irrelevant'
          ]);
          $revisionContent = Kirby\Cms\Form::for($virtualPage)->values();
          $revisions[$idx]['content'] = $revisionContent;
        }

        return $revisions;
      }
    ]
  ],

];
