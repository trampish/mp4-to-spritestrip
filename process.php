<?php

  // Config =====================

  $demoAssets = [
    'https://raw.githubusercontent.com/blaiprat/jquery.animateSprite/master/scripts/jquery.animateSprite.min.js',
    'http://code.jquery.com/jquery-1.11.1.js',
  ];

  $lb = "\r\n"; // Appends linebreaks to all prompts

  // Helper Functions =====================

  function isCLI() {
      return (php_sapi_name() === 'cli' OR defined('STDIN'));
  }

  function getAssets($items) {

    if (!file_exists('demo/')) {
      mkdir('demo');
    }

    foreach ($items as $item) {
      if (!file_exists('demo/' . basename($item))) {
        file_put_contents('demo/' . basename($item), file_get_contents($item));
      }
    }

  }

  function userPrompt($message, $validator=null) {
      if (!isCLI()) return null;

      print($message);
      $handle = fopen ('php://stdin','r');
      $line = rtrim(fgets($handle), "\r\n");

      if (is_callable($validator) && !call_user_func($validator, $line)) {
          print("Invalid Entry.\r\n");
          return userPrompt($message, $validator);
      } else {
          print("Continuing...\r\n");
          return $line;
      }
  }

  function video_attributes($video, $ffmpeg) {

    $command = $ffmpeg . ' -i ' . $video . ' -vstats 2>&1';
    $output = shell_exec($command);

    $regex_sizes = "/Video: ([^,]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/";
    if (preg_match($regex_sizes, $output, $regs)) {
        $codec = $regs [1] ? $regs [1] : null;
        $width = $regs [3] ? $regs [3] : null;
        $height = $regs [4] ? $regs [4] : null;
     }

    $regex_duration = "/Duration: ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2}).([0-9]{1,2})/";
    if (preg_match($regex_duration, $output, $regs)) {
        $hours = $regs [1] ? $regs [1] : null;
        $mins = $regs [2] ? $regs [2] : null;
        $secs = $regs [3] ? $regs [3] : null;
        $ms = $regs [4] ? $regs [4] : null;
    }

    return array ('codec' => $codec,
            'width' => $width,
            'height' => $height,
            'hours' => $hours,
            'mins' => $mins,
            'secs' => $secs,
            'ms' => $ms
    );

  }

  function createDemo($projectName, $dimensions) {
    getAssets($demoAssets);

    // To Do: demo template, injection of relevant variables for jquery.AnimateSprite

  }

  // Validator Functions =====================

  function validateSetLangCode($str) {
      return preg_match("/^[A-Z0-9]{3}-[A-Z]{2}$/", $str);
  }

  function validateYN($str) {
      $str = strtoupper($str);
      if (($str == "Y") || ($str == "N")) {
        return $str;
      } else {
        return false;
      }
  }

  // Initial Set Up =====================

    // If you don't have ffmpeg installed, you're gonna have a bad time.
    if (!file_exists('ffmpeg/ffmpeg')) {
      if (!file_exists('ffmpeg')) { mkdir('ffmpeg'); }
      echo "You don't have FFmpeg installed properly." . $lb . "Please put ensure the binary for your system is in the `ffmpeg` folder." . $lb;
      exit;
    }

  //
  // Layout of each step:
  //
  // {int: step id} => [
  //   prompt => '{string: prompt seen by user, always be polite}',
  //   variable => '{string: name of variable to be assigned value from user input}',
  //   validator => '{string: name of validator function user input run against}',
  //   defaultval => '{default value if not provided by user}'
  // ],
  //

  $steps = [
    0 => [
      prompt => 'Please specify a video file using a relative path:',
      variable => 'inputFile',
      validator => null,
      alt => null
    ],
    1 => [
      prompt => 'Please specify a start time using the HH:MM:SS.MS format, milleseconds optional:',
      variable => 'startTime',
      validator => null,
      alt => null,
      defaultval => '00:00:00'
    ],
    2 => [
      prompt => 'Please specify clip length using the HH:MM:SS.MS format, milleseconds optional:',
      variable => 'clipLength',
      validator => null,
      alt => null
    ],
    3 => [
      prompt => 'Please specify a framerate:',
      variable => 'framerate',
      validator => null,
      alt => null,
      defaultval => 7.0
    ],
    4 => [
      prompt => 'Please specify an output resolution, default is the video file\'s native resolution: WxH',
      variable => 'resolution'
      validator => null,
      alt => null
    ],
    5 => [
      prompt => 'Please specify an output filename:',
      variable => 'projectName',
      validator => null,
      alt => null,
      defaultval => 'untitled.jpg'
    ],
    6 => [
      prompt => 'Do you want to assemble the spritestrip immediately? Y/N',
      variable => 'buildImageNow',
      validator => 'validateYN',
      alt => null
    ],
    7 => [
      prompt => 'Do you want to create a demo file? Y/N',
      variable => 'buildDemoNow',
      validator => 'validateYN',
      alt => null
    ],
    7 => [
      prompt => 'Please enter the set / language codes. Use the format \'SET-EN\', where SET is the three-letter set code and EN is the two-letter lang code.',
      validator => 'validateSetLangCode',
      alt => 'SET-EN'
    ]
  ];

  // We're going to iterate through the prompts, collecting data and assembling our FFmpeg command
  $i = 0;
  foreach($steps as $step) {

    $input = userPrompt($step['prompt'] . $lb, $step['validator']) ?: $step['alt'];

    // Can we mutate user input in the validator functions?
    if ($i == 0) {
      // Get file attributes
      $vidAttr = video_attributes($input, 'ffmpeg/ffmpeg');
    }

    $i++;
  }

  $ffcommand = './ffmpeg/ffmpeg -ss ' . $startTime . ' -t ' . $clipLength . ' -i ' . $inputFile . ' -r ' . $framerate . ' -s ' . $resolution;

  // ./ffmpeg -ss 00:00:21 -t 00:00:13 -i cycling.mov -r 7.0 -s 622x350 cycling/cycling%4d.jpg

  // To Do: JPG Assembly (GD?)
  // To Do: Cropping / Resizing ?
  // To Do: Demo Files Assemby

  //  $code = userPrompt($steps[$x]['prompt'] . $lb, $steps[$x]['validator']) ?: $steps[$x]['alt'];
  //  var_dump($code);
