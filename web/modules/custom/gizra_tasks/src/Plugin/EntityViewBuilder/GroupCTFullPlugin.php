<?php

namespace Drupal\gizra_tasks\Plugin\EntityViewBuilder;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\og\Og;
use Drupal\og\OgAccessInterface;
use Drupal\og\OgMembershipInterface;
use Drupal\og\MembershipManagerInterface;
use Drupal\server_general\EntityViewBuilder\NodeViewBuilderAbstract;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  protected OgAccessInterface $ogAccess;
  protected MembershipManagerInterface $membershipManager;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    AccountInterface $current_user,
    EntityRepositoryInterface $entity_repository,
    LanguageManagerInterface $language_manager,
    OgAccessInterface $og_access,
    MembershipManagerInterface $membership_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $current_user, $entity_repository, $language_manager);

    $this->ogAccess = $og_access;
    $this->membershipManager = $membership_manager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('entity.repository'),
      $container->get('language_manager'),
      $container->get('og.access'),
      $container->get('og.membership_manager')
    );
  }

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
  public function buildFull(array $build, NodeInterface $entity): array {
    $original = $build;
    $content = [];

    $current_user = $this->currentUser;

    if ($current_user->isAuthenticated()) {
      $user = $this->entityTypeManager->getStorage('user')->load($current_user->id());

      if (Og::isGroup($entity->getEntityTypeId(), $entity->bundle())) {
        if ($entity->getOwnerId() != $user->id()) {
          $access = $this->ogAccess->userAccess($entity, 'subscribe', $user);
          if ($access && $access->isAllowed()) {
            if ($this->membershipManager->getMembership($entity, $current_user->id()) === NULL) {
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

    $original['group_subscribe'] = [
      '#type' => 'container',
      'content' => $content,
    ];

    return $original;
  }

}
