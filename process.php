<?php

  // Config

  $demoAssets = [
    'https://raw.githubusercontent.com/blaiprat/jquery.animateSprite/master/scripts/jquery.animateSprite.min.js',
    'http://code.jquery.com/jquery-1.11.1.js',
  ];


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

  function createDemo($projectName, $dimensions) {
    getAssets($demoAssets);

    //

  }

  // Example =====================

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


  $steps = [
    0 => [
      prompt => 'Please specify a video file using a relative path:',
      validator => null,
      alt => null
    ],
    1 => [
      prompt => 'Please specify a start time using the HH:MM:SS.MS format, milleseconds optional:',
      validator => null,
      alt => null,
      defaultval => '00:00:00'
    ],
    2 => [
      prompt => 'Please specify clip length using the HH:MM:SS.MS format, milleseconds optional:',
      validator => null,
      alt => null
    ],
    3 => [
      prompt => 'Please specify a video file using a relative path:',
      validator => null,
      alt => null
    ],
    4 => [
      prompt => 'Please specify a framerate:',
      validator => null,
      alt => null,
      defaultval => 7.0
    ],
    5 => [
      prompt => 'Please specify an output resolution, default is the video file\'s native resolution:',
      validator => null,
      alt => null
    ],
    6 => [
      prompt => 'Please specify an output filename:',
      validator => null,
      alt => null,
      defaultval => 'untitled.jpg'
    ],
    7 => [
      prompt => 'Do you want to assemble the spritestrip immediately? Y/N',
      validator => validateYN,
      alt => null
    ],
    8 => [
      prompt => 'Please enter the set / language codes. Use the format \'SET-EN\', where SET is the three-letter set code and EN is the two-letter lang code.',
      validator => 'validateSetLangCode',
      alt => 'SET-EN'
    ]
  ];

  getAssets();

  $append = "\r\n"; // Appends linebreaks to all prompts

  $x = 7;

  $code = userPrompt($steps[$x]['prompt'] . $append, $steps[$x]['validator']) ?: $steps[$x]['alt'];


  $ffcommand = './ffmpeg/ffmpeg -ss ' . $startTime . ' -t ' . $clipLength . '';

  //foreach ($steps as $step) {
  //  $value = userPrompt($steps[$x]['prompt'] . $append, $steps[$x]['validator']) ?: $steps[$x]['alt'];
  //}

  $code = userPrompt($steps[$x]['prompt'] . $append, $steps[$x]['validator']) ?: $steps[$x]['alt'];
  var_dump($code);

  // ./ffmpeg -ss 00:00:21 -t 00:00:13 -i cycling.mov -r 7.0 -s 622x350 cycling/cycling%4d.jpg
