<?php

declare(strict_types=1);

namespace Drupal\metatag_custom_tags\Plugin\metatag\Tag;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Render\PlainTextOutput;
use Drupal\Component\Utility\Random;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\metatag\Plugin\metatag\Tag\MetaNameBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Custom configured meta tags will be available.
 *
 * The meta tag's values will be based upon this annotation.
 *
 * @MetatagTag(
 *   id = "metatag_custom_tag",
 *   deriver = "Drupal\metatag_custom_tags\Plugin\Derivative\MetaTagCustomTagDeriver",
 *   label = @Translation("Custom Tag"),
 *   description = @Translation("This plugin will be cloned from these settings for each custom tag."),
 *   name = "metatag_custom_tag",
 *   weight = 1,
 *   group = "metatag_custom_tags",
 *   type = "string",
 *   secure = FALSE,
 *   multiple = TRUE
 * )
 */
class MetaTagCustomTag extends MetaNameBase {

  /**
   * The string this tag uses for the element itself.
   *
   * @var string
   */
  protected $htmlElement;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    // Additional elements.
    $this->htmlElement = $plugin_definition['htmlElement'] ?? 'meta';
    $this->htmlNameAttribute = $plugin_definition['htmlNameAttribute'] ?? 'name';
    $this->htmlValueAttribute = $plugin_definition['htmlValueAttribute'] ?? 'content';
  }

  /**
   * Generate the HTML tag output for a meta tag.
   *
   * @return array
   *   A render array.
   */
  public function output(): array {
    // Start with the original output.
    $output = parent::output();

    // Change the 'tag' value to the HTML element defined in this plugin.
    foreach ($output as $key => $tag) {
      $output[$key]['#tag'] = $this->htmlElement;
    }

    return $output;
  }

  /**
   * The xpath string which identifies this meta tag presence on the page.
   *
   * @return array
   *   A list of xpath-formatted string(s) for matching a field on the page.
   */
  public function getTestOutputExistsXpath(): array {
    return ["//" . $this->htmlElement . "[@" . $this->htmlNameAttribute . "='{$this->name}']"];
  }

  /**
   * The xpath string which identifies this meta tag's output on the page.
   *
   * @param array $values
   *   The field names and values that were submitted.
   *
   * @return array
   *   A list of xpath-formatted string(s) for matching a field on the page.
   */
  public function getTestOutputValuesXpath(array $values): array {
    $xpath_strings = [];
    foreach ($values as $value) {
      $xpath_strings[] = "//" . $this->htmlElement . "[@" . $this->htmlNameAttribute . "='{$this->name}' and @" . $this->htmlValueAttribute . "='{$value}']";
    }
    return $xpath_strings;
  }

}
