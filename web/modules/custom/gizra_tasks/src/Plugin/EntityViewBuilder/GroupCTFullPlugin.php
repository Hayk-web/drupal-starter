<?php

namespace Drupal\gizra_tasks\Plugin\EntityViewBuilder;

use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\og\Og;
use Drupal\og\OgMembershipInterface;
use Drupal\server_general\EntityViewBuilder\NodeViewBuilderAbstract;
use Drupal\user\Entity\User;

/**
 * The "Node Group" plugin.
 *
 * @EntityViewBuilder(
 *   id = "node.group",
 *   label = @Translation("Node - Group"),
 *   description = "Node view builder for Group bundle."
 * )
 */
class GroupCTFullPlugin extends NodeViewBuilderAbstract {
  /**
   * Build full view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildFull(array $build, NodeInterface $entity) {
    // Always keep the original build metadata.
    $original = $build;

    $current_user = \Drupal::currentUser();

    // Initialize content block.
    $content = [];

    if ($current_user->isAuthenticated()) {
      $user = User::load($current_user->id());

      if (Og::isGroup($entity->getEntityTypeId(), $entity->bundle())) {
        if ($entity->getOwnerId() != $user->id()) {
          $access_manager = \Drupal::service('og.access');
          if (($access = $access_manager->userAccess($entity, 'subscribe', $user)) && $access->isAllowed()) {
            $membership_manager = \Drupal::service('og.membership_manager');
            if ($membership_manager->getMembership($entity, $current_user->id()) == NULL) {
              $url = Url::fromRoute('og.subscribe', [
                'entity_type_id' => $entity->getEntityTypeId(),
                'group' => $entity->id(),
                'og_membership_type' => OgMembershipInterface::TYPE_DEFAULT,
              ]);
              $content[] = [
                '#theme' => 'group_subscribe_message',
                '#name' => $user->getDisplayName(),
                '#group' => $entity->label(),
                '#url' => $url,
                '#request' => TRUE,
              ];
            }
          }
        }
        else {
          $content[] = [
            '#theme' => 'group_subscribe_message',
          ];
        }
      }
    }

    // Append your content to the original build safely.
    $original['group_subscribe'] = [
      '#type' => 'container',
      'content' => $content,
    ];

    return $original;
  }
}
