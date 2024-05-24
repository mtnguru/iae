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
print "start db.php - " . $date . "\n";
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
$accountSwitcher = \Drupal::service('account_switcher');
$accountSwitcher->switchTo(new UserSession(['uid' => 1]));

// Retrieve Atoms from DB
$elements = build_atom_list();

// Query for all atoms and build list of isotopes.  Atom name should have it all?

// Search for the Isotope
$found = false;
foreach($element['isotopes'] as &$isotope) {
  $protons = $isotope[0]->field__protons_value;
  $p = $fields[$headers['A'][0]];
  if ($protons == $p) {
    $found = true;
    break;
  }
}

exit(0);

/////////////////////////////////////////////////////////////////////////////////////////

function build_atom_list() {
  $elements = AtomizerInit::queryElements();
  $atoms = AtomizerInit::queryAtoms('full')['results'];

  foreach ($elements as &$element) {
    $nelements[$element['atomicNumber']] = &$element;
  }

  $natoms = count($atoms);
  print("Atoms: $natoms\n");

  $natoms = 0;
  // Go through each atom and count number of atoms per element
  foreach ($atoms as $atom) {
    $id = $atom->nid;
    $eid = $atom->field_element_target_id;
    if (!empty($elements[$eid])) {
      $natoms++;
      $elements[$eid]['isotopes'][$atom->field__protons_value][] = $atom;
      if (empty($elements[$eid]['numIsotopes'])) {
        $elements[$eid]['numIsotopes'] = 1;
      } else {
        $elements[$eid]['numIsotopes']++;
      }
    }
  }

  print ("Number of atoms: $natoms\n");

  return $nelements;
}


