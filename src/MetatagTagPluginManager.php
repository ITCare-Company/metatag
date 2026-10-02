<?php

namespace Drupal\metatag;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\metatag\Attribute\MetatagTag;

/**
 * A Plugin to manage your meta tag type.
 */
class MetatagTagPluginManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    $subdir = 'Plugin/metatag/Tag';

    parent::__construct($subdir, $namespaces, $module_handler, NULL, MetatagTag::class, 'Drupal\metatag\Annotation\MetatagTag');

    $this->alterInfo('metatag_tags');

    $this->setCacheBackend($cache_backend, 'metatag_tags');
  }

}
