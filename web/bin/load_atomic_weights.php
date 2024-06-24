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

// Read in atomic_weights file
$handle = fopen("atomic_weights", "r");
$ln = 0;
if ($handle) {
  $first = true;
  while (($line = fgets($handle)) !== false) {
    $ln++;
    if ($line[0] == ' ') {
      $words = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
      $protons = $words[0];
      $mass = $words[1];
      $abundance = (empty($words[2])) ? null : $words[2] * 100;
    }
    else {
      $words = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
      $an = $words[0];
      $protons = $words[2];
      $mass = $words[3];
      $abundance = (empty($words[4])) ? null : $words[4] * 100;
    }

    $element = $elements[$an];
    $elementName = $element['name'];
    print "Atom $elementName $protons\n";
    if (empty($element['isotopes'][$protons])) {
      // New atom - create it
      print "Create Atom: $elementName $protons\n";
      $isotope = null;
      $name = $element['name'] . ' ' . $protons;
      $atom = Node::create([
        'title' => $element['name'] . ' ' . $protons,
        'nid' => NULL,
        'created' => REQUEST_TIME,
        'uid' => 1,
        'type' => 'atom',
        'status' => TRUE,
        'field_mass_actual' => $mass,
        'field__protons' => $protons,
        'field__inner_electrons' => $protons - $element['atomicNumber'],
        'field__outer_electrons' => $element['atomicNumber'],
        'field_abundance' => $abundance,
        'field_element' => $element['nid'],
        'field_approval' => 'stats',
        'field_an_isotope' => sprintf("%3s:%3s\n", $element['atomicNumber'], $protons),
        'field_stability' => $abundance ? 34 : 35,
//      'field_isotope' => $protons - 2 * $element['atomicNumber'],
      ]);
      $mass = atomizer_calculate_mass($protons, $protons - $element['atomicNumber'], $element['atomicNumber']);
      $atom->field_mass_calculated->value = $mass;
      if ($first) {
        $atom->save();
        print "Created atom " . $atom->id() . "\n";
//      $first = false;
      }
    }
    else {
      $natoms = count($element['isotopes'][$protons]);
      foreach ($element['isotopes'][$protons] as $isotope) {
        $atom = Node::load($isotope->nid);
        if ($natoms > 1) {
          if ($atom->field_approval->value == 'stats') {
            $atom->delete();
          }
          continue;
        }

        print "Update isotope: " . $atom->label() . " " . $atom->field__protons->value . "\n";
        $atom->field_mass_actual->setValue($mass);
        $atom->field_abundance->setValue($abundance > 0 ? $abundance : null);

        $protons = $atom->field__protons->value;
        $inner = $atom->field__inner_electrons->value = $protons - $element['atomicNumber'];
        $outer = $atom->field__outer_electrons->value = $element['atomicNumber'];

        $atom->field_mass_calculated->value = atomizer_calculate_mass($protons, $inner, $outer);
        $atom->field_an_isotope->value = sprintf("%3s:%3s\n", $element['atomicNumber'], $protons);

        if (!$atom->field_mass_actual->isEmpty()) {
          $actual = $atom->field_mass_actual->value;
          if ($actual > 1000000) {
            $atom->field_mass_actual->value = $actual / 1000000;
          }
        }
        $atom->save();
      };
    }
  }

  fclose($handle);
} else {
  // error opening the file.
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


