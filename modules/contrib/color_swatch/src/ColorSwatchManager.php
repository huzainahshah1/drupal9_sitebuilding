<?php

namespace Drupal\color_swatch;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Class ColorSwatchManager
 *
 * @package Drupal\color_swatch
 */
class ColorSwatchManager {

  /**
   * Theme Extension List.
   *
   * @var \Drupal\Core\Extension\ThemeExtensionList
   */
  private $themeExtensionList;

  /**
   * Module Handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * Theme Manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  private $themeManager;

  public function __construct(ThemeManagerInterface $themeManager, ThemeExtensionList $themeExtensionList, ModuleHandlerInterface $moduleHandler) {
    $this->themeManager = $themeManager;
    $this->themeExtensionList = $themeExtensionList;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * Get theme color swatch placeholders.
   *
   * @param string $theme
   *
   * @return array
   */
  public function getThemeColorSwatchPlaceholdersInfo(string $theme): array {
    $info = $this->themeExtensionList->getExtensionInfo($theme);
    return $info['color-swatch']['placeholders'] ?? [];
  }

  /**
   * Get available theme color swatches
   *
   * @param string $theme
   *
   * @return array
   */
  public function getThemeColorSwatchesInfo(string $theme): array {
    $info = $this->themeExtensionList->getExtensionInfo($theme);
    return $info['color-swatch']['swatches'] ?? [];
  }

  /**
   * Get theme color swatch info of specific color swatch.
   *
   * @param string $theme
   * @param string $swatchName
   *
   * @return array
   */
  public function getThemeColorSwatchInfo(string $theme, string $swatchName): array {
    $swatches = $this->getThemeColorSwatchesInfo($theme);
    return $swatches[$swatchName] ?? $this->getColorSwatchThemeSettings($theme)['settings'][$swatchName] ?? [];
  }

  /**
   * Get the default color swatch information.
   *
   * @param string $theme
   *
   * @return string
   */
  public function getThemeDefaultColorSwatchName(string $theme): string {
    $info = $this->themeExtensionList->getExtensionInfo($theme);
    return $info['color-swatch']['default'] ?? '';
  }

  /**
   * Validate if the input of the hex is in the format #ffffff, #fff, ffffff or fff
   *
   * @param string $hex
   *
   * @return string
   */
  public function validateHex(string $hex): string {
    // Complete patterns like #ffffff or #fff
    if (preg_match('/^#([0-9a-fA-F]{6})$/', $hex) || preg_match('/^#([0-9a-fA-F]{3})$/', $hex)) {
      // Remove #
      $hex = substr($hex, 1);
    }

    // Complete patterns without # like ffffff or 000000
    if (preg_match('/^([0-9a-fA-F]{6})$/', $hex)) {
      return $hex;
    }

    // Short patterns without # like fff or 000
    if (preg_match('/^([0-9a-f]{3})$/', $hex)) {
      // Spread to 6 digits
      return $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    // If input value is invalid return black
    return '000000';
  }

  /**
   * Get HSL values from rgb hex.
   *
   * @param string $hex
   *
   * @return array
   */
  public function hexToHsl(string $hex): array {
    //Validate Hex Input
    $hex = $this->validateHex($hex);

    $hexValues = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
    $rgb_val = array_map(static function($part) {
      return hexdec($part) / 255;
    }, $hexValues);

    $max_val = max($rgb_val);
    $min_val = min($rgb_val);
    $lightness = ($max_val + $min_val) / 2;

    if ($max_val === $min_val) {
      $hue = $saturation = 0;
      return $this->buildHslArray($hue, $saturation, $lightness);
    }

    $diff = $max_val - $min_val;
    $saturation = $lightness > 0.5 ? $diff / (2 - $max_val - $min_val) : $diff / ($max_val + $min_val);

    $hue = 0;
    if ($max_val === $rgb_val[0]) {
      $hue = ($rgb_val[1] - $rgb_val[2]) / $diff + ($rgb_val[1] < $rgb_val[2] ? 6 : 0);
    }

    if ($max_val === $rgb_val[1]) {
      $hue = ($rgb_val[2] - $rgb_val[0]) / $diff + 2;
    }

    if ($max_val === $rgb_val[2]) {
      $hue = ($rgb_val[0] - $rgb_val[1]) / $diff + 4;
    }

    $hue /= 6;

    return $this->buildHslArray($hue, $saturation, $lightness);
  }

  /**
   * Get a color swatch.
   *
   * @param string $theme
   * @param string $swatchName
   *
   * @return \Drupal\color_swatch\ColorSwatch
   */
  public function getColorSwatch(string $theme, string $swatchName): ColorSwatch {
    return new ColorSwatch($this, $theme, $swatchName);
  }

  /**
   * Get current active theme.
   *
   * @return \Drupal\Core\Theme\ActiveTheme
   */
  public function getActiveTheme(): ActiveTheme {
    return $this->themeManager->getActiveTheme();
  }

  /**
   * Get color swatch theme settings.
   *
   * @param string $theme
   *
   * @return array|mixed|null
   */
  public function getColorSwatchThemeSettings(string $theme) {
    return theme_get_setting('color_swatch', $theme);
  }

  /**
   * Check if the theme has color swatches.
   *
   * @param string $theme
   *
   * @return bool
   */
  public function hasColorSwatches(string $theme): bool {
    return !empty($this->getThemeDefaultColorSwatchName($theme)) &&
      !empty($this->getThemeColorSwatchPlaceholdersInfo($theme)) &&
      !empty($this->getThemeColorSwatchesInfo($theme));
  }

  /**
   * Get the color swatch that is active.
   *
   * @param string $theme
   *
   * @return \Drupal\color_swatch\ColorSwatch
   */
  public function getActiveColorSwatch(string $theme): ColorSwatch {
    $swatchName = $this->getActiveColorSwatchName($theme);
    $colorSwatch = $this->getColorSwatch($theme, $swatchName);
    $this->moduleHandler->alter('active_color_swatch', $colorSwatch);

    return $colorSwatch;
  }

  /**
   * Get active color swatch name.
   *
   * @param string $theme
   *
   * @return string
   */
  public function getActiveColorSwatchName(string $theme): string {
    $themeSettings = $this->getColorSwatchThemeSettings($theme);
    $swatchName = $this->getThemeDefaultColorSwatchName($theme);
    if (isset($themeSettings['active']) && !empty($this->getThemeColorSwatchInfo($theme, $swatchName))) {
      $swatchName = $themeSettings['active'];
    }

    $this->moduleHandler->alter('active_color_swatch_name', $colorSwatch);

    return $swatchName;
  }

  /**
   * Get the module handler.
   *
   * @return \Drupal\Core\Extension\ModuleHandlerInterface
   */
  public function getModuleHandler(): ModuleHandlerInterface {
    return $this->moduleHandler;
  }

  /**
   * Build the hsl array.
   *
   * @param int|float $hue
   * @param int|float $saturation
   * @param int|float $lightness
   *
   * @return array
   */
  private function buildHslArray($hue, $saturation, $lightness): array {
    return ['h' => $hue * 360, 's' => $saturation * 100, 'l' => $lightness * 100];
  }

}
