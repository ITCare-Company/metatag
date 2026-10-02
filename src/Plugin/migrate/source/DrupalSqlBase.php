<?php

namespace Drupal\metatag\Plugin\migrate\source;

use Drupal\Component\Plugin\DependentPluginInterface;
use Drupal\Core\Entity\DependencyTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\Exception\RequirementsException;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Local replacement for the removed migrate_drupal DrupalSqlBase.
 *
 * Drupal 12 removed the migrate_drupal module's
 * \Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase class outright,
 * with no replacement (see the change record linked below: "there is no
 * replacement"). This module's own D6/D7 migrate source plugins (the
 * MetatagField/MetatagFieldInstance/MetatagDefaults/NodewordsField/
 * NodewordsFieldInstance family) extended that class only for its generic
 * Drupal-database helper methods,
 * none of which are migrate_drupal-specific. This class ports those methods
 * verbatim from the last available core (11.x) source, re-based directly on
 * core's still-present generic SqlBase so the module's source plugin keeps
 * working unchanged on Drupal 12.
 *
 * @see https://www.drupal.org/node/3533564
 * @see \Drupal\migrate\Plugin\migrate\source\SqlBase
 */
abstract class DrupalSqlBase extends SqlBase implements DependentPluginInterface {

  use DependencyTrait;

  /**
   * The contents of the system table.
   *
   * @var array
   */
  protected $systemData;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Retrieves all system data information from the source Drupal database.
   *
   * @return array
   *   List of system table information keyed by type and name.
   */
  public function getSystemData() {
    if (!isset($this->systemData)) {
      $this->systemData = [];
      try {
        $results = $this->select('system', 's')
          ->fields('s')
          ->execute();
        foreach ($results as $result) {
          $this->systemData[$result['type']][$result['name']] = $result;
        }
      }
      catch (\Exception $e) {
        // The table might not exist, for example in tests.
      }
    }
    return $this->systemData;
  }

  /**
   * {@inheritdoc}
   */
  public function checkRequirements() {
    parent::checkRequirements();
    if ($this->pluginDefinition['requirements_met'] === TRUE) {
      if ($source_module = $this->getSourceModule()) {
        if ($this->moduleExists($source_module)) {
          if (isset($this->pluginDefinition['minimum_version'])) {
            $minimum_version = (int) $this->pluginDefinition['minimum_version'];
            $installed_version = (int) $this->getModuleSchemaVersion($source_module);
            if ($minimum_version > $installed_version) {
              throw new RequirementsException('Required minimum version ' . $this->pluginDefinition['minimum_version'], ['minimum_version' => $this->pluginDefinition['minimum_version']]);
            }
          }
        }
        else {
          throw new RequirementsException('The module ' . $source_module . ' is not enabled in the source site.', ['source_module' => $source_module]);
        }
      }
    }
  }

  /**
   * Retrieves a module schema_version from the source Drupal database.
   *
   * @param string $module
   *   Name of module.
   *
   * @return mixed
   *   The current module schema version on the origin system table or FALSE
   *   if not found.
   */
  protected function getModuleSchemaVersion($module) {
    $system_data = $this->getSystemData();
    return $system_data['module'][$module]['schema_version'] ?? FALSE;
  }

  /**
   * Checks if a given module is enabled in the source Drupal database.
   *
   * @param string $module
   *   Name of module to check.
   *
   * @return bool
   *   TRUE if module is enabled on the origin system, FALSE if not.
   */
  protected function moduleExists($module) {
    $system_data = $this->getSystemData();
    return !empty($system_data['module'][$module]['status']);
  }

  /**
   * Reads a variable from a source Drupal database.
   *
   * @param string $name
   *   Name of the variable.
   * @param mixed $default
   *   The default value.
   *
   * @return mixed
   *   The variable value, or $default if not found.
   */
  protected function variableGet($name, $default) {
    try {
      $result = $this->select('variable', 'v')
        ->fields('v', ['value'])
        ->condition('name', $name)
        ->execute()
        ->fetchField();
    }
    catch (\Exception $e) {
      // The table might not exist.
      $result = FALSE;
    }
    return $result !== FALSE ? unserialize($result) : $default;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    if (isset($this->configuration['constants']['entity_type'])) {
      $this->addDependency('module', $this->entityTypeManager->getDefinition($this->configuration['constants']['entity_type'])->getProvider());
    }
    if (isset($this->configuration['constants']['module'])) {
      $this->addDependency('module', $this->configuration['constants']['module']);
    }
    return $this->dependencies;
  }

}
