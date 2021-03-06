<?php

namespace Drupal\Tests\entity_browser_example\FunctionalJavascript;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Entity browser example module.
 *
 * @group entity_browser
 */
class EntityBrowserExampleTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['entity_browser_example'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests Entity Browser example module.
   */
  public function testExampleInstall() {
    // If we came this far example module installed successfully.
  }

}
