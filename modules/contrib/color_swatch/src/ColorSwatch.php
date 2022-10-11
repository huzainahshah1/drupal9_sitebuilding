<?php

namespace Drupal\color_swatch;

use Drupal;

/**
 * Class ColorSwatchManager
 *
 * @package Drupal\color_swatch
 */
class ColorSwatch {

  /**
   * Name of the color swatch.
   *
   * @var string
   */
  protected $name;

  /**
   * @var \Drupal\color_swatch\ColorSwatchManager
   */
  private $colorSwatchManager;

  /**
   * @var string
   */
  private $theme;

  /**
   * ColorSwatch constructor.
   *
   * @param \Drupal\color_swatch\ColorSwatchManager $colorSwatchManager
   * @param string $theme
   * @param string $name
   */
  public function __construct(ColorSwatchManager $colorSwatchManager, string $theme, string $name) {
    $this->colorSwatchManager = $colorSwatchManager;
    $this->setName($name);
    $this->setTheme($theme);
  }

  /**
   * Get the color swatch name.
   *
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * Set the name.
   *
   * @param string $name
   *
   * @return \Drupal\color_swatch\ColorSwatch
   */
  public function setName(string $name): self {
    $this->name = $name;
    return $this;
  }

  /**
   * Get the theme name.
   *
   * @return string
   */
  public function getTheme(): string {
    return $this->theme;
  }

  /**
   * Set theme.
   *
   * @param string $theme
   *
   * @return \Drupal\color_swatch\ColorSwatch
   */
  public function setTheme(string $theme): self {
    $this->theme = $theme;
    return $this;
  }

  /**
   * Get the placeholders.
   *
   * @return array
   */
  public function getPlaceholders(): array {
    return $this->colorSwatchManager->getThemeColorSwatchPlaceholdersInfo($this->getTheme());
  }

  /**
   * Is this color swatch the default.
   *
   * @return bool
   */
  public function isDefault(): bool {
    return $this->colorSwatchManager->getThemeDefaultColorSwatchName($this->getTheme()) === $this->getName();
  }

  /**
   * Get the RGB hex value.
   *
   * @param string $placeholder
   *
   * @return string
   */
  public function getHex(string $placeholder): string {
    $colorSwatchInfo = $this->colorSwatchManager->getThemeColorSwatchInfo($this->getTheme(), $this->getName());

    if (!isset($colorSwatchInfo[$placeholder])) {
      return '#000000';
    }

    return (string) $colorSwatchInfo[$placeholder];
  }

  /**
   * Get the HSL values.
   *
   * @param string $placeholder
   *
   * @return float[]|int[]
   */
  public function getHsl(string $placeholder): array {
    return $this->colorSwatchManager->hexToHsl($this->getHex($placeholder));
  }

  /**
   * Get the RGB values.
   *
   * @param string $placeholder
   *
   * @return array
   */
  public function getRgb(string $placeholder): array {
    $hex = str_replace('#', '', $this->getHex($placeholder));
    $rgb = str_split($hex, strlen($hex)/3);

    return [
      'r' => hexdec($rgb[0]),
      'g' => hexdec($rgb[1]),
      'b' => hexdec($rgb[2]),
    ];
  }

  /**
   * Render the css for the color swatch.
   *
   * @return string
   */
  public function renderCss(): string {
    $build = $this->buildCss();
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = Drupal::service('renderer');
    return (string) $renderer->renderPlain($build);
  }

  /**
   * Build the css render array for the color swatch.
   *
   * @return array
   */
  public function buildCss(): array {
    return [
      '#theme' => 'color_swatch_css',
      '#color_swatch' =>$this,
    ];
  }

  /**
   * Get the HSL Hue value.
   *
   * @param string $placeholder
   *
   * @return float|int
   */
  public function getHue(string $placeholder) {
    return $this->colorSwatchManager->hexToHsl($this->getHex($placeholder))['h'];
  }

  /**
   * Get the HSL Saturation value.
   *
   * @param string $placeholder
   *
   * @return float|int
   */
  public function getSaturation(string $placeholder) {
    return $this->colorSwatchManager->hexToHsl($this->getHex($placeholder))['s'];
  }

  /**
   * Get the HSL Lightness value.
   *
   * @param string $placeholder
   *
   * @return float|int
   */
  public function getLightness(string $placeholder) {
    return $this->colorSwatchManager->hexToHsl($this->getHex($placeholder))['l'];
  }

  /**
   * Get contrast ratio (0-10), larger than 5 is a light color.
   *
   * @param string $placeholder
   *
   * @return int
   */
  public function getContrastRatio(string $placeholder): int {
    // hexColor RGB
    $R1 = hexdec(substr($this->getHex($placeholder), 1, 2));
    $G1 = hexdec(substr($this->getHex($placeholder), 3, 2));
    $B1 = hexdec(substr($this->getHex($placeholder), 5, 2));

    // Black RGB
    $blackColor = "#000000";
    $R2BlackColor = hexdec(substr($blackColor, 1, 2));
    $G2BlackColor = hexdec(substr($blackColor, 3, 2));
    $B2BlackColor = hexdec(substr($blackColor, 5, 2));

    // Calc contrast ratio
    $L1 = 0.2126 * (($R1 / 255) ** 2.2) +
      0.7152 * (($G1 / 255) ** 2.2) +
      0.0722 * (($B1 / 255) ** 2.2);

    $L2 = 0.2126 * (($R2BlackColor / 255) ** 2.2) +
      0.7152 * (($G2BlackColor / 255) ** 2.2) +
      0.0722 * (($B2BlackColor / 255) ** 2.2);

    if ($L1 > $L2) {
      return (int) (($L1 + 0.05) / ($L2 + 0.05));
    }

    return (int) (($L2 + 0.05) / ($L1 + 0.05));
  }

  /**
   * Get the color swatch manager;
   *
   * @return \Drupal\color_swatch\ColorSwatchManager
   */
  public function colorSwatchManager(): ColorSwatchManager {
    return $this->colorSwatchManager;
  }

}
