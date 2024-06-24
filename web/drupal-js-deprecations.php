<?php

/**
 * @file
 * Checks contrib/custom code for use of deprecated core js libraries.
 *
 * Usage:
 * drush scr drupal-js-deprecations
 */

// Get a list of deprecated core js libraries.
/** @var \Drupal\Core\Asset\LibraryDiscoveryInterface $libraryService */
$libraryService = \Drupal::service('library.discovery');
$libraries = $libraryService->getLibrariesByExtension('core');
$libraries = array_keys(array_filter($libraries, static function($library) {
  return isset($library['deprecated']);
}));
$deprecated_libraries = preg_filter('/^/', 'core/', $libraries);

// Get lists of contrib and custom modules and themes to scan.
$modules = getExtensions(\Drupal::service('extension.list.module'));
$themes  = getExtensions(\Drupal::service('extension.list.theme'));

// Show possible deprecations for modules.
foreach ($modules as $name => $value) {
  $libraries = $libraryService->getLibrariesByExtension($name);
  foreach ($libraries as $library) {
    if (!isset($library['dependencies'])) {
      continue;
    }
    foreach($library['dependencies'] as $dependency) {
      if (in_array($dependency, $deprecated_libraries, TRUE)) {
        echo "Module $name uses deprecated core dependency $dependency and probably needs a fix.\n";
      }
    }
  }
}

// Show possible deprecations for themes.
foreach ($themes as $name => $value) {
  $libraries = $libraryService->getLibrariesByExtension($name);
  foreach ($libraries as $library) {
    if (!isset($library['dependencies'])) {
      continue;
    }
    foreach($library['dependencies'] as $dependency) {
      if (in_array($dependency, $deprecated_libraries, TRUE)) {
        echo "Theme $name uses deprecated core dependency $dependency and probably needs a fix.\n";
      }
    }
  }
}

/**
 * Get a list of contributed / custom modules or themes.
 *
 * @param $extensionListService
 *   The service to use for discovery (modules or themes).
 *
 * @return mixed
 */
function getExtensions($extensionListService) {
  $extensions = $extensionListService->getAllAvailableInfo();
  $extensions = array_filter($extensions, static function($extension) {
    return $extension['package'] !== 'Core';
  });
  ksort($extensions);
  return $extensions;
}
