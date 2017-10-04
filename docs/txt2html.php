<?php

function dump($value, $varname = "", $level = 0, $dumper = "") {
  if ($varname) {
    $varname .= " = ";
  }

  if ($level == -1) {
    $trans[' '] = '&there4;';
    $trans["\t"] = '&rArr;';
    $trans["\n"] = '&para;;';
    $trans["\r"] = '&lArr;';
    $trans["\0"] = '&oplus;';

    return strtr(htmlspecialchars($value), $trans);
  }
  if ($level == 0) {
    $dumper = '<pre>' . $varname;
  }

  $type = gettype($value);
  $dumper .= $type;

  if ($type == 'string') {
    $dumper .= '(' . strlen($value) . ')';
    $value = dump($value, "", -1);
  } elseif ($type == 'boolean') {
    $value = ($value ? 'true' : 'false');
  } elseif ($type == 'object') {
    $props = get_class_vars(get_class($value));
    $dumper .= '(' . count($props) . ') <u>' . get_class($value) . '</u>';
    foreach ($props as $key => $val) {
      $dumper .= "\n" . str_repeat("\t", $level + 1) . $key . ' => ';
      $dumper .= dump($value->$key, "", $level + 1);
    }
    $value = '';
  } elseif ($type == 'array') {
    $dumper .= '(' . count($value) . ')';
    foreach ($value as $key => $val) {
      $dumper .= "\n" . str_repeat("\t", $level + 1) . dump($key, "", -1) . ' => ';
      $dumper .= dump($val, "", $level + 1);
    }
    $value = '';
  }
  $dumper .= " <b>$value</b>";
  if ($level == 0) {
    $dumper .= '</pre>';
  }

  return $dumper;
}

function pdump($value, $varname = '') {
  print('<span style="text-align: left">' . dump($value, $varname) . '</span>');
}

function debug($value, $varname = '') {
  return pdump($value, $varname);
}

function buf_print($string) {
  global $output_buffer;

  $output_buffer .= $string;
}

if (substr(getcwd(), -4) != 'docs') {
  $path_prefix = 'docs/';
} else {
  $path_prefix = '';
}

$output_buffer = '';

$filename = 'changelog';

$input = file_get_contents($path_prefix . $filename . '.txt');
//$input = iconv('CP1251', 'UTF-8', $input);

$input = preg_replace("/\r\n\d\d\d\d\-\d\d\-\d\d\ \d\d\:\d\d/", "[D] $0", $input);

while (strpos($input, "\r\n\r\n") !== false) {
  $input = str_replace("\r\n\r\n", "\r\n", $input);
}
while (strpos($input, "~~~") !== false) {
  $input = str_replace("~~~", "~~", $input);
}
$input = str_replace("\r\n~~", "~~", $input);

while (strpos($input, "===") !== false) {
  $input = str_replace("===", "==", $input);
}
$input = str_replace("\r\n==", "==", $input);

$input = preg_split("/\r\n(.+)[\~\=]{2}/", $input, -1, PREG_SPLIT_DELIM_CAPTURE + PREG_SPLIT_NO_EMPTY); // 

$prev_chapter_is_header = false;
$output = array();
$buffer = array();
foreach ($input as &$chapter) {
  $chapter = preg_split("/(\r\n[\[])/", $chapter, -1, PREG_SPLIT_NO_EMPTY); // , PREG_SPLIT_DELIM_CAPTURE

  if (count($chapter) == 1 && !$prev_chapter_is_header) {
    if (!empty($chapter)) {
      $output[] = $buffer;
      $buffer = array();
      $buffer['name'] = $chapter[0];
    }
    $prev_chapter_is_header = true;
  } else {
    $prev_chapter_is_header = false;
    foreach ($chapter as &$note) {
      $note = explode("\r\n", $note);
      $new_note = true;
      $buf_str = '';

      $note_out = array();

      foreach ($note as &$line) {
        if (!$line) {
          continue;
        }
        if ($new_note) {
          // 78 - 3 = 75
          $note_out['style'] = $line[0];
          $line = substr($line, 3);
        }

        $buf_str .= $line;
        if (mb_strlen($line, 'utf-8') < ($new_note ? 75 : 79)) {
          if (!isset($note_out['name'])) {
            $note_out['name'] = $buf_str;
          } else {
            $note_out['lines'][] = $buf_str;
          }
          $buf_str = '';
        }

        $new_note = false;
      }
      $buffer['content'][] = $note_out;
    }
  }
}
$output[] = $buffer;

buf_print('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru" dir="LTR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css"><!--
body {font-family: monospace}
li, ol, ul, pre {margin: 0}
div {margin-bottom: 10px}

.important {color: green; font-weight: bold;}
.added {color: green;}
.removed {font-style: italic; color: red}
.changed {color: blue}
.fixed {color: red}
.admin {font-style: italic;}
.module {color: purple; font-weight: bold;}
.todo {}
.date {margin-bottom: 0; color: grey}
--></style>
</head>
<body>
');
// ; text-decoration: underline;
$styles = array(
  '!' => 'important',
  '+' => 'added',
  '-' => 'removed',
  '~' => 'changed',
  '%' => 'fixed',
  '@' => 'admin',
  '#' => 'module',
  '*' => 'todo',
  'D' => 'date',
);

foreach ($output as $chapter) {
  if (!$chapter) {
    continue;
  }

  buf_print("<h1>{$chapter['name']}</h1>\r\n");
  foreach ($chapter['content'] as $block) {
    buf_print("<div class=\"{$styles[$block['style']]}\">" . ($block['style'] != 'D' ? "[{$block['style']}]&nbsp;" : ''));
    buf_print(preg_replace("/\s{2,10}/", " ", $block['name']) . '<br />');
    if (isset($block['lines'])) {
      $last_spaces = '';
      $depth = array();
      foreach ($block['lines'] as $line) {
        if (preg_match("/^(\s+)(\d*|\s)\.*\s*(.*)/", $line, $matches)) {
          //$line = strlen($matches[1]) . '/' . $matches[2] . '/' . $matches[3];
          $line = $matches[3];
          if (strlen($matches[1]) > strlen($last_spaces)) {
            if ($matches[2]) {
              buf_print("<ol>\r\n");
            } else {
              buf_print("<ul>\r\n");
            }
            buf_print('<li>');
            $last_spaces = $matches[1];
            $depth[] = $matches[2];
          } elseif (strlen($matches[1]) < strlen($last_spaces) && count($depth)) {
            if (array_pop($depth)) {
              buf_print("</ol>\r\n");
            } else {
              buf_print("</ul>\r\n");
            }
            $last_spaces = $matches[1];
            buf_print('<li>');
          } elseif (strlen($last_spaces) == strlen($matches[1])) {
            if ($matches[2] == '' && $depth[count($depth) - 1] != '') {
              buf_print("</ol>\r\n");
              buf_print("<ul>\r\n");
            } elseif ($matches[2] != '' && $depth[count($depth) - 1] == '') {
              buf_print("</ul>\r\n");
              buf_print("<ol>\r\n");
            }
            $depth[count($depth) - 1] = $matches[2];
            buf_print('<li>');
          }
        }
        $line = preg_replace("/\s{2,10}/", " ", $line);
        buf_print($line . "<br />\r\n");
      }
      while (count($depth)) {
        if (array_pop($depth)) {
          buf_print("</ol>\r\n");
        } else {
          buf_print("</ul>\r\n");
        }
      }
    }
    buf_print("</div>\r\n");
  }
}
buf_print("</body>\r\n</html>\r\n");

$html = file_get_contents($path_prefix . 'html/' . $filename . '.html');
if ($html != $output_buffer) {
  file_put_contents($path_prefix . 'html/' . $filename . '.html', $output_buffer);
  if (!$path_prefix) {
    print($output_buffer);
  }
  exit(1);
}
exit(0);
