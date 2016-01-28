<?php
/**
 * @file
 * Contains \Drupal\role_delegation\Access\RoleDelegationAccessCheck.
 */

namespace Drupal\role_delegation\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\role_delegation\RoleDelegationPermissions;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Checks access for displaying configuration translation page.
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
      return FALSE;
    }
    // Check if they can edit users. In that case, the Roles tab is not needed.
    if ($account->hasPermission('administer users')) {
      return FALSE;
    }
    // Check access to role assignment page.
    if ($account->hasPermission('administer permissions')) {
      return TRUE;
    }
    $perms = (new RoleDelegationPermissions)->rolePermissions();
    foreach ($perms as $perm) {
      if ($account->hasPermission($perm)) {
        return TRUE;
      }
    }

    return FALSE;

  }
}
