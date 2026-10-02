<?php

namespace Drupal\metatag\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Hook\Order\Order;
use Drupal\taxonomy\TermInterface;

/**
 * Hook implementations for metatag.
 */
class MetatagPageAttachmentsHooks {

  /**
   * Implements hook_page_attachments_alter().
   *
   * D12 (change record 3496788): moved from a bare procedural function to
   * this OOP method so the order: Order::Last parameter actually takes
   * effect. Core's HookCollectorPass only reads a #[Hook] attribute's order
   * parameter when it is on a real class method (OOP scan branch); the
   * procedural-file scan branch never does (see
   * HookCollectorPass::collectModuleHookImplementations(),
   * core/lib/Drupal/Core/Hook/HookCollectorPass.php:397-405 vs. 424-466).
   * The old metatag_module_implements_alter() procedural reorder is left in
   * place as dead code for pre-11.2 BC.
   */
  #[Hook('page_attachments_alter', order: Order::Last)]
  public function pageAttachmentsAlter(array &$attachments) {
    $route_match = \Drupal::routeMatch();
    // Can be removed once https://www.drupal.org/node/2282029 is fixed.
    if ($route_match->getRouteName() == 'entity.taxonomy_term.canonical' && ($term = $route_match->getParameter('taxonomy_term')) && $term instanceof TermInterface) {
      _metatag_remove_duplicate_entity_tags($attachments);
    }
  }

}
