<?php
/**
 * @file
 * Contains \Drupal\role_delegation\Access\RoleDelegationAccessCheck.
 */

namespace Drupal\role_delegation\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\role_delegation\RoleDelegationPermissions;

/**
 * Checks access for displaying configuration edit user pages.
 */
class RoleDelegationAccessCheck implements AccessInterface {

  /**
   * A custom access check.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   */
  public function access(AccountInterface $account) {
    // Check access to user profile page.
    if($account->hasPermission('access user profiles')) {
      return AccessResult::forbidden()->cachePerPermissions();
    }
    // Check if they can edit users. In that case, the Roles tab is not needed.
    if ($account->hasPermission('administer users')) {
      return AccessResult::forbidden()->cachePerPermissions();
    }
    // Check access to role assignment page.
    if ($account->hasPermission('administer permissions')) {
      return AccessResult::allowed()->cachePerPermissions();
    }
    $perms = new RoleDelegationPermissions();
    foreach ($perms->rolePermissions() as $perm) {
      if ($account->hasPermission($perm)) {
        return AccessResult::allowed()->cachePerPermissions();
      }
    }
    return AccessResult::forbidden();
  }
}
