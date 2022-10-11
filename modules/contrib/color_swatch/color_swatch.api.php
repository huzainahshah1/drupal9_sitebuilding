<?php

use Drupal\color_swatch\ColorSwatch;

/**
 * Change the color swatch on the fly.
 *
 * @param \Drupal\color_swatch\ColorSwatch $colorSwatch
 */
function hook_active_color_swatch_alter(ColorSwatch $colorSwatch) {
  $colorSwatch->setName('custom');
  $colorSwatch->setTheme('some_other_theme_with_color_swatches');
}
