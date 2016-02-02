<?php

/**
 * @file
 * Contains \Drupal\role_delegation\RoleDelegationPermissions.
 */

namespace Drupal\role_delegation;

use Drupal\Core\Routing\UrlGeneratorTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\user\Entity;

/**
 * Defines a class containing permission callbacks.
 */
class RoleDelegationPermissions {

  use StringTranslationTrait;
  use UrlGeneratorTrait;

  /**
   * Returns an array of node type permissions.
   *
   * @return array
   */
  public function rolePermissions() {
    $perms = array();

    $roles = user_roles(TRUE);
    unset($roles[AccountInterface::AUTHENTICATED_ROLE]);
    unset($roles['administrator']);

    foreach ($roles as $rid => $role) {
      $perms["assign {$role->get('id')} role"] = array(
        'title' => $this->t('Assign %role role', array('%role' => $role->label())),
      );
    }

    return $perms;
  }
}
