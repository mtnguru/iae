<?php

use Drupal\atomizer\AtomizerInit;
use Drupal\az_content\AzContentQuery;
use Drupal\Core\Session\UserSession;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\DrupalKernel;
use Drupal\Core\Site\Settings;


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

