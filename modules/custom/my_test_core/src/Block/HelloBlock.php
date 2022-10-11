<?php

namespace Drupal\my_test_core\hello_world\Plugin\Block;

use Drupal\Core\Block\BlockBase;
/**
* Provides a 'Hello' block.
 *
 * @Block(
 *   id = "hello_block",
 *   admin_label = @Translation("Hello block"),
 *   category = @Translation("Hello"),
  * )
 */

class HelloBlock extends BlockBase {

  /* ...[snip]... */

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'hello_block',
      '#data' => ['age' => '31', 'DOB' => '2 May 2000'],
    ];
  }

  /**
   * Implements hook_theme().
   */
  function hello_world_theme() {
    return [
      'hello_block' => [
        'variables' => [
          'data' => [],
        ],
      ],
    ];
  }











}
