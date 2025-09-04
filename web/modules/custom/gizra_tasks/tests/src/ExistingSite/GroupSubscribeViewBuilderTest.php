<?php

namespace Drupal\Tests\gizra_tasks\ExistingSite;

use Drupal\Core\Url;
use Drupal\og\OgMembershipInterface;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\Tests\server_general\ExistingSite\ServerGeneralTestBase;

/**
 * Tests the PEVB plugin rendering subscribe message for OG Group.
 *
 * @group gizra_tasks
 */
class GroupSubscribeViewBuilderTest extends ServerGeneralTestBase {

  use UserCreationTrait;

  /**
   * Tests that the subscription message is shown to allowed user.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Behat\Mink\Exception\ResponseTextException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testSubscribeMessageIsRendered(): void {
    // Create a user who will be offered to subscribe.
    $user = $this->createUser();

    // Create a user who will own the group.
    $owner = $this->createUser();

    // Create a group node owned by someone else.
    $group = $this->createNode([
      'type' => 'group',
      'title' => 'Test Group',
      'uid' => $owner->id(),
      'status' => 1,
    ]);

    $this->assertEquals($owner->id(), $group->getOwnerId());

    // Log in the test user.
    $this->drupalLogin($user);

    // We can browse pages.
    $this->drupalGet($group->toUrl());
    $this->assertSession()->statusCodeEquals(200);
    // Assert the rendered message is present.
    $this->assertSession()->pageTextContains(
      "Hi {$user->getDisplayName()}, click here if you would like to subscribe to this group called {$group->label()}."
    );

    // Additional access check logic.
    $access_manager = \Drupal::service('og.access');
    $access = $access_manager->userAccess($group, 'subscribe', $user);

    $this->assertTrue($access->isAllowed(), 'User has subscribe access.');

    // Also check there is no existing membership.
    $membership_manager = \Drupal::service('og.membership_manager');
    $membership = $membership_manager->getMembership($group, $user->id());
    $this->assertNull($membership, 'User is not yet a member of the group.');

    // Check that subscription URL is correctly rendered.
    $url = Url::fromRoute('og.subscribe', [
      'entity_type_id' => $group->getEntityTypeId(),
      'group' => $group->id(),
      'og_membership_type' => OgMembershipInterface::TYPE_DEFAULT,
    ])->toString();

    $this->assertSession()->responseContains($url);
  }

}
