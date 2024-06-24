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
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;

// Get the current date
$date = date('Y/m/d h:i:s');
print "start load_atomic_weights.php - " . $date . "\n";
print "PHP_SAPI: " . PHP_SAPI . "\n";

if (PHP_SAPI !== 'cli') {
  return;
}

define('DRUPAL_DIR', '/home/az/drupal/docroot');
// Bootstrap Drupal
$autoloader = require_once '../autoload.php';
require_once '../core/includes/bootstrap.inc';


$request = Request::createFromGlobals();
Settings::initialize(dirname(dirname(__DIR__)), DrupalKernel::findSitePath($request), $autoloader);
$kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod')->boot();
$kernel->boot();
$kernel->prepareLegacyRequest($request);

// Switch from anonymous user to admin
// $accountSwitcher = \Drupal::service('account_switcher');
// $accountSwitcher->switchTo(new UserSession(['uid' => 1]));

$atom = Node::load(249);
print "Update isotope: " . $atom->label() . " " . $atom->field__protons->value . "\n";

//    $name = $atom->label();

//    if (strpos($name, "Carbon") === FALSE) continue;

      // Save the half life
//    $hl = $fields[$headers['half_life'][0]];
//    if ($hl != "STABLE" && !empty($fields[$headers['half_life'][0]])) {
//      $atom->field_half_life->setValue($fields[$headers['half_life']]);
//    }

//  if (!$atom->field_mass_actual->isEmpty()) {
//    $actual = $atom->field_mass_actual->value;
//    if ($actual > 1000000) {
//      $atom->field_mass_actual->value = $actual / 1000000;
//    }
//  }

$atom->save();

exit(0);
