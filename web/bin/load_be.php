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
print "start load_be.php - " . $date . "\n";
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

$headers = [
  'Z'            => [],
  'symb'         => [],
  'A'            => [],
  'half_life'    => [],
  'unit'         => [],
  'decay'        => [],
  'decay %'      => [],
  'magn. dipole' => [],
  'Binding/A'    => [],
  'Total BE MeV' => [],
  'atomic mass'  => [],
];

// Read in atomic_weights file
$file = fopen("livechart.csv", "r");
if ($file) {
  $line = fgetcsv($file);
  foreach ($headers as $name => &$value) {
    $col = 0;
    foreach ($line as $field) {
      if (trim($field) == $name) {
        $headers[$name][] = $col;
      }
      $col++;
    }
  }

  $ln = 0;
  while (($fields = fgetcsv($file)) !== false) {
    $ln++;
//  if ($ln > 100) break;

    // Search for the element
    $found = false;
    foreach ($elements as &$element) {
      $symb = $fields[$headers['symb'][0]];  // Get the symbol for this element
      if ($symb == $element['symbol']) {
        $found = true;
        break;
      }
    }

    if (!$found) continue;  // Element not found

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

    if (!$found) continue;  // Isotope not found

    foreach ($isotope as $iso) {
      $atom = Node::load($iso->nid);
      $name = $atom->label();
//    if (strpos($name, "Carbon") === FALSE) continue;
      print "Update isotope: " . $atom->label() . " - " . $atom->field__protons->value . "\n";

//    'decay'        => [],
//    'decay %'      => [],
//    'magn. dipole' => [],
//    'Binding/A'    => [],
//    'Total BE MeV' => [],
//    'atomic mass'  => [],

      // Save the half life
      $hl = $fields[$headers['half_life'][0]];
      $units = $fields[$headers['unit'][0]];
      if ($hl == "STABLE") {
        $hl = "Stable";
      }
      if (!empty($fields[$headers['half_life'][0]])) {
        $len = strlen($units);
        if (strlen($units) > 0) {
          $atom->field_half_life->setValue($hl . ' ' . $units);
        } else {
          $atom->field_half_life->setValue($hl);
        }
      }

      $mag = $fields[$headers['magn. dipole'][0]];
      if ($mag > 0) {
        $atom->field_magnetic_dipole->value = $mag;
      }

      $binding = $fields[$headers['Binding/A'][0]];
      if ($binding) {
        $atom->field_be_nucleon->value = $binding;
        $atom->field_be_actual->value = $binding * $atom->field__protons->value / 1000;
      }

      $decay = $fields[$headers['decay'][0]];
      if ($decay) {
        $val = '';
        for ($i = 0; $i < count($headers['decay']); $i++) {
          $decay = trim($fields[$headers['decay'][$i]]);
          $perc = trim($fields[$headers['decay %'][$i]]);
          if ($decay != '') {
            if ($i) {
              $val .= ', ';
            }
            $val .= $decay;
            if ($perc != '') {
              $val .= ' ' . $perc;
            }
          }
        }
        if (strlen($val)) {
//        $fart = 5;
          $atom->field_decay->value = $val;
        }
      }

      $atom->save();
    }
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


