<?php

declare(strict_types=1);

namespace Drupal\metatag\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a MetatagGroup attribute object.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class MetatagGroup extends Plugin {

  /**
   * Constructs a MetatagGroup attribute.
   *
   * @param string $id
   *   The group's internal ID, in machine name format.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $label
   *   The name of the group.
   * @param string $description
   *   (optional) Description of the group.
   * @param int|null $weight
   *   (optional) Weight of the group.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   */
  public function __construct(
    public readonly string $id,
    public readonly TranslatableMarkup $label,
    public readonly string $description = '',
    public readonly ?int $weight = NULL,
    public readonly ?string $deriver = NULL,
  ) {}

}
