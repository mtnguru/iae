<?php

namespace Drupal\az_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Provides a 'Book navigation' block.
 *
 * @Block(
 *   id = "az_sam_tools",
 *   admin_label = @Translation("AZ SAM Tools Links"),
 *   category = @Translation("AZ Menu")
 * )
 */
class AzSamToolsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Add header to tools links - should I remove this?
    $build['tools_title'] = [
      '#markup' => '<h4>' .  t('Tools') . '</h4>',
    ];

    // Add link to the Atom Builder
    if (\Drupal::currentUser()->hasPermission('atomizer display atom builder')) {
      $build['atom_builder'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['atom-builder-link']],
        'link' => [
          '#type' => 'link',
          '#title' => t('Atom Builder'),
          '#attributes' => [
            'title' => t('Interactive program to build atoms according to SAM.'),
          ],
          '#url' => Url::fromUri('base:atomizer/atom-builder', [
            'absolute' => TRUE,
          ]),
        ],
      ];
    }

    // Add link to the Atom Viewer
      $build['atom_viewer'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['atom-viewer-link']],
        'link' => [
          '#type' => 'link',
          '#title' => t('SAM Atom Viewer'),
          '#attributes' => [
            'title' => t('Interactive program to view the elements according to SAM.'),
          ],
          '#url' => Url::fromUri('base:atomizer/atom-viewer', [
            'absolute' => TRUE,
          ]),
        ],
      ];

    // Add link to SAM Periodic Table
    $build['sam_periodic_table'] = [
      '#type' => 'container',
      'link' => [
        '#type' => 'link',
        '#title' => t('SAM Periodic Table'),
        '#attributes' => [
          'title' => t('Periodic table used in research for SAM - shows images of each elements nucleus and other critical information..'),
          'target' => '_blank',
        ],
        '#url' => Url::fromUri('base:atomizer/pte', [
          'absolute' => TRUE,
        ]),
      ],
    ];

    $build['links_title'] = [
      '#markup' => '<h4>' .  t('Links') . '</h4>',
    ];

    // Add link to the Dynamic Periodic Table
    $build['nuclides'] = [
      '#type' => 'container',
      'link' => [
        '#type' => 'link',
        '#title' => t('View Live Chart of Nuclides'),
        '#attributes' => [
          'title' => t('Dynamic chart that allows users to explore the Chart of Nuclides.'),
          'target' => '_blank',
        ],
        '#url' => Url::fromUri('https://www-nds.iaea.org/relnsd/vcharthtml/VChartHTML.html'),
      ],
    ];

    // Add link to List of Oxidation states of the elements
    $build['oxidation_states'] = [
      '#type' => 'container',
      'link' => [
        '#type' => 'link',
        '#title' => t('View Oxidation States'),
        '#attributes' => [
          'title' => t('List of Oxidation states of the elements'),
          'target' => '_blank',
        ],
        '#url' => Url::fromUri('https://en.wikipedia.org/wiki/List_of_oxidation_states_of_the_elements'),
      ],
    ];

    // Add link to the Dynamic Periodic Table
    $build['periodic_table'] = [
      '#type' => 'container',
      'link' => [
        '#type' => 'link',
        '#title' => t('View Dynamic Periodic Table'),
        '#attributes' => [
          'title' => t('Dynamic periodic table that let\'s you explore properties of the elements.'),
          'target' => '_blank',
        ],
        '#url' => Url::fromUri('https://ptable.com/'),
      ],
    ];

    return $build;
  }
}

