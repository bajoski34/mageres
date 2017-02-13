<?php
const NO_SUBTOPIC = '0__no_subtopic__';

if (!isset($argv[2])) {
  echo "Usage: php csv2md.php input_file output_file.csv" . PHP_EOL;
  exit(1);
}

$csv = $argv[1];
if (!file_exists($csv)) {
  echo "File not found: $csv" . PHP_EOL;
  exit(1);
}

$outfile = $argv[2];
if ($csv == $outfile) {
  echo "Cannot write on input file!" . PHP_EOL;
  exit(1);
}

$countents = [];
$out = <<<OUT
# Magento Resources
A curated list of useful Magento technical resources.
Resources are listed **alphabetically** within each category.

OUT;
$topic = '';
$prevtopic = '';
$subtopic = '';
$prevsubtopic = '';
if (($handle = fopen($csv, "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        list($topic, $subtopic, $name, $url, $description) = $data;

        if (!isset($contents[$topic])) {
          $contents[$topic] = [];
        }

        if (empty($subtopic)) {
          $subtopic = NO_SUBTOPIC;
        }

        if (!isset($contents[$topic][$subtopic])) {
          $contents[$topic][$subtopic] = [];
        }

        $contents[$topic][$subtopic][$name] = [
            'url' => $url,
            'description' => $description,
        ];
    }
    fclose($handle);
}

foreach ($contents as $topic => $subtopics)
{
  $out .= sprintf(PHP_EOL . "## %s" . PHP_EOL, $topic);
  ksort($subtopics);
  foreach ($subtopics as $subtopic => $subtopicdata) {
    if (NO_SUBTOPIC != $subtopic) {
      $out .= sprintf(PHP_EOL . "### %s" . PHP_EOL, $subtopic);
    }
    ksort($subtopicdata);
    foreach ($subtopicdata as $name => $data) {
      $out .= sprintf("* [%s](%s)%s%s" . PHP_EOL, $name, $data['url'], empty($data['description']) ? '' : ' - ', $data['description']);
    }
  }
}

$fp = fopen($outfile, 'w');
fwrite($fp, $out);
fclose($fp);
#echo $out;
#print_r($contents);
