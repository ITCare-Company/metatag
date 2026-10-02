<?php

declare(strict_types=1);

namespace Drupal\metatag\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a MetatagTag attribute object.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class MetatagTag extends Plugin {

  /**
   * Constructs a MetatagTag attribute.
   *
   * @param string $id
   *   The meta tag plugin's internal ID, in machine name format.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $label
   *   The display label/name of the meta tag plugin.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|string $description
   *   (optional) A longer explanation of what the field is for.
   * @param string $name
   *   (optional) Proper name of the actual meta tag itself.
   * @param string $group
   *   (optional) The group this meta tag fits in, corresponds to a
   *   MetatagGroup plugin.
   * @param int|null $weight
   *   (optional) Weight of the tag.
   * @param string $type
   *   (optional) Type of the meta tag. Should be either 'date', 'image',
   *   'integer', 'label', 'string' or 'uri'.
   * @param bool $secure
   *   (optional) TRUE if URL must use HTTPS.
   * @param bool $multiple
   *   (optional) TRUE if more than one is allowed.
   * @param bool $long
   *   (optional) TRUE if the tag should use a text area.
   * @param bool $absoluteUrl
   *   (optional) TRUE if the URL value(s) must be absolute.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    public readonly string $id,
    public readonly TranslatableMarkup $label,
    public readonly TranslatableMarkup|string $description = '',
    public readonly string $name = '',
    public readonly string $group = '',
    public readonly ?int $weight = NULL,
    public readonly string $type = '',
    public readonly bool $secure = FALSE,
    public readonly bool $multiple = FALSE,
    public readonly bool $long = FALSE,
    public readonly bool $absoluteUrl = FALSE,
    public readonly ?string $deriver = NULL,
  ) {}

}
