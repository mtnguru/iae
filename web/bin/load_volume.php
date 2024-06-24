#!/usr/bin/env php

<?php

/**
 * @file
 * A command line application to generate proxy classes.
 */

use Drupal\atomizer\AtomizerInit;
use Drupal\az_content\AzContentQuery;
use Drupal\Core\Session\UserSession;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;

// Get the current date
$date = date('Y/m/d h:i:s');
print "start load_volume.php - " . $date . "\n";
print "PHP_SAPI: " . PHP_SAPI . "\n";

if (PHP_SAPI !== 'cli') {
  return;
}

// Bootstrap Drupal
$autoloader = require_once '../autoload.php';
require_once '../core/includes/bootstrap.inc';
$request = Request::createFromGlobals();
Settings::initialize(dirname(dirname(__DIR__)), DrupalKernel::findSitePath($request), $autoloader);
$kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod')->boot();
$kernel->boot();
$kernel->prepareLegacyRequest($request);

// Switch from anonymous user to admin
$accountSwitcher = \Drupal::service('account_switcher');
$accountSwitcher->switchTo(new UserSession(['uid' => 1]));

// Read in volume csv file
$f = fopen("volume.csv", "r");
$ln = 0;
if ($f) {
  while (($fields = fgetcsv($f)) !== false) {
    // 0  nid
    // 1  atom name
    // 2  # protons
    // 3  X
    // 4  Y
    // 5  Z
    // 6  Radius
    // 7  Volume
    // 8  Ratio


    $ln++;
    if ($fields[0] == 'nodeID' || empty($fields[0])) continue;
    $nid = $fields[0];
    $atom = Node::load($nid);

    if ($atom->field_bounding_sphere->isEmpty()) {
      $volume = Paragraph::create([
        'type' => 'bounding_area',
        'field_center_x' => $fields[3],
        'field_center_y' => $fields[4],
        'field_center_z' => $fields[5],
        'field_radius'   => $fields[6],
        'field_volume'   => $fields[7],
        'field_ratio'    => $fields[8],
      ]);
      $volume->save();

      $atom->field_bounding_sphere->setValue($volume);
      $atom->save();
    } else {
      $pid =  $atom->field_bounding_sphere->target_id;
      $volume = Paragraph::load($pid);
      $arr = $volume->toArray();
      $volume->save();
    }

    print "$ln field 1 $fields[0]\n";
  }

  fclose($f);
} else {
  print ("Could not open file\n");
}

exit(0);

