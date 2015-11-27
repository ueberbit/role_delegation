<?php
/**
 * Created by PhpStorm.
 * User: ggoetlund
 * Date: 24.11.15
 * Time: 14:24
 */

namespace Drupal\role_delegation;


use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;

class RoleDelegationHelper {

  /**
   * @param array $form
   * @param \Drupal\user\UserInterface $user
   * @return array
   */
  public static function addRoleDelegationElement(array $form, UserInterface $user) {
    $current_user = \Drupal::currentUser();

    $roles_current = $user->getRoles(TRUE);

    $roles_delegate = array();

    $roles = user_roles(TRUE);
    unset($roles[AccountInterface::AUTHENTICATED_ROLE]);
    unset($roles['administrator']);

    foreach ($roles as $rid => $role) {
      if ($current_user->hasPermission('assign all roles') || $current_user->hasPermission("assign {$role->get('id')} role")) {
        $roles_delegate[$rid] = isset($form['account']['roles']['#options'][$rid]) ? $form['account']['roles']['#options'][$rid] : $role->get('id');
      }
    }

    if (empty($roles_delegate)) {
      // No role can be assigned.
      return $form;
    }
    if (!isset($form['account'])) {
      $form['account'] = array(
        '#type' => 'value',
        '#value' => $user,
      );
    }

    $default_options = array();

    foreach ($roles_current as $role) {
      if (in_array($role, $roles_delegate)) {
        $default_options[$role] = $role;
      }
    }

    // Generate the form items.
    $form['account']['roles_change'] = array(
      '#type' => 'checkboxes',
      '#title' => isset($form['account']['roles']['#title']) ? $form['account']['roles']['#title'] : t('Roles'),
      '#options' => $roles_delegate,
      '#default_value' => array_keys($default_options),
      '#description' => isset($form['account']['roles']['#description']) ? $form['account']['roles']['#description'] : t('Change roles assigned to user.'),
    );

    return $form;
  }
}
