<?php

/**
 * @file
 * Contains \Drupal\role_delegation\Tests\RoleDelegationTest.
 */

namespace Drupal\role_delegation\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\user\Entity\Role;

/**
 * Base class for Role Delegation tests.
 *
 * @group role_delegation
 */
class RoleDelegationTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('user', 'role_delegation');

  /**
   * Use the standard profile.
   *
   * @var
   */
  protected $profile = 'standard';

  /**
   * A field storage to use in this test class.
   *
   * @var \Drupal\field\Entity\FieldStorageConfig
   */
  protected $rid_high;

  /**
   * The field used in this test class.
   *
   * @var \Drupal\field\Entity\FieldConfig
   */
  protected $rid_low;

  /**
   * A field storage to use in this test class.
   *
   * @var \Drupal\field\Entity\FieldStorageConfig
   */
  protected $user_high;

  /**
   * The field used in this test class.
   *
   * @var \Drupal\field\Entity\FieldConfig
   */
  protected $user_low;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create roles
    $this->rid_high = $this->drupalCreateRole(array(), 'high', 'high');
    $this->rid_low  = $this->drupalCreateRole(array(), 'low', 'low');

    // Create users
    $this->user_high = $this->drupalCreateUser(array('administer users'));
    $this->user_low  = $this->drupalCreateUser(array('administer users'));

    // Create privileged user and log in
    $this->drupalLogin($this->drupalCreateUser(array('administer users', 'administer permissions')));

    // Assign permissions to roles
    $this->assignPermissionToRole('assign low role', $this->rid_high);  // 'high' can assign 'low'

    // Assign roles to users
    $this->assertTrue(
      $this->assignRoleToUser($this->rid_high, $this->user_high),
      'Assign high role to high user'
    );

  }


  /**
   * Assign or remove one role to/from one user.
   *
   * The logged in user must have the "administer users"
   * permission in order for this function to succeed.
   *
   * @param $rid
   *   The role id of the role to assign or remove.
   * @param $user
   *   The user object of the user to assign/remove the role to.
   * @param $assign
   *   TRUE (the default) to assign the role, or
   *   FALSE to remove it.
   *
   * @return bool
   *  TRUE or FALSE depending on whether the permission was
   *  successfully assigned or removed.
   */
  protected function assignRoleToUser($rid, $user, $assign = TRUE) {

    $this->drupalGet("user/{$user->id()}/edit");

    if (count($this->xpath("//input[@name='roles[$rid]']"))) {
      $name = "roles[$rid]";
    }
    elseif (count($this->xpath("//input[@name='roles_change[$rid]']"))) {
      $name = "roles_change[$rid]";
    }
    else {
      return FALSE;
    }
    $this->drupalPostForm(NULL, array($name => $assign), t('Save'));

    $elements = $this->xpath("//input[@name='$name']");
    $debug = empty($elements[0]['checked']);
    return isset($elements[0]) && ($assign XOR empty($elements[0]['checked']));
  }

  /**
   * Assign or remove one permission to/from one role, and assert
   * that the result succeeded.
   *
   * @param $permission
   *   The name of the permission to assign or remove.
   * @param $rid
   *   The role id of the role to assign/remove the permission to/from.
   * @param $assign
   *   TRUE (the default) to assign the permission, or
   *   FALSE to remove it.
   *
   * @return bool
   *   TRUE or FALSE depending on whether the permission was
   *   successfully assigned or removed.
   */
  protected function assignPermissionToRole($permission, $rid, $assign = TRUE) {
    $name = "{$rid}[{$permission}]";
    $this->drupalPostForm("admin/people/permissions/$rid", array($name => $assign), t('Save permissions'));
    $elements = $this->xpath("//input[@name='$name']");
    $this->assertTrue(
      isset($elements[0]) && ($assign XOR empty($elements[0]['checked'])),
      ($assign ? 'Assign' : 'Remove') . ' permission "' . $permission . '" ' . ($assign ? 'to' : 'from') . " role $rid."
    );
  }

}
