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

    $build = [];
    $current_user = \Drupal::currentUser();

    // Only act if user is logged in.
    if ($current_user->isAuthenticated()) {
      $user = User::load($current_user->id());
      // Check if the node is a group.
      if (Og::isGroup($entity->getEntityTypeId(), $entity->bundle())) {
        if ($entity->getOwnerId() != $user->id()) {
          /** @var \Drupal\og\OgAccess $access_manager */
          $access_manager = \Drupal::service('og.access');
          if (($access = $access_manager->userAccess($entity, 'subscribe', $user)) && $access->isAllowed()) {
            /** @var \Drupal\og\MembershipManager $membership_manager */
            $membership_manager = \Drupal::service('og.membership_manager');
            if ($membership_manager->getMembership($entity, $current_user->id()) == NULL) {
              $parameters = [
                'entity_type_id' => $entity->getEntityTypeId(),
                'group' => $entity->id(),
                'og_membership_type' => OgMembershipInterface::TYPE_DEFAULT,
              ];
              $url = Url::fromRoute('og.subscribe', $parameters);
              $build[] = [
                '#theme' => 'group_subscribe_message',
                '#name' => $user->getDisplayName(),
                '#group' => $entity->label(),
                '#url' => $url,
                '#request' => true
              ];
            }
          }
        } else {
          $build[] = [
            '#theme' => 'group_subscribe_message'
          ];
        }
      }
    }

    return $build;
  }
}
