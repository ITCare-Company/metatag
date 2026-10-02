<?php

namespace Drupal\metatag;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\metatag\Attribute\MetatagGroup;

/**
 * A Plugin to manage your meta tag group.
 */
class MetatagGroupPluginManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    $subdir = 'Plugin/metatag/Group';

    parent::__construct($subdir, $namespaces, $module_handler, NULL, MetatagGroup::class, 'Drupal\metatag\Annotation\MetatagGroup');

    $this->alterInfo('metatag_groups');

    $this->setCacheBackend($cache_backend, 'metatag_groups');
  }

}
