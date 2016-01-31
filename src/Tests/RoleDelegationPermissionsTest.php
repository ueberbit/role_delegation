<?php

/**
 * @file
 * Contains \Drupal\role_delegation\Tests\RoleDelegationPermissionsTest.
 */

namespace Drupal\role_delegation\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\user\Entity\Role;
/**
 * Functional tests for permissions.
 *
 * @group role_delegation
 */
class RoleDelegationPermissionsTest extends RoleDelegationTest {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
}

  public static function getInfo() {
    return array(
      'name' => t('Permissions'),
      'description' => t('Check that role assignment permissions are enforced.'),
      'group' => t('Role Delegation'),
    );
  }

  /**
   * Check that high role can assign low role.
   */
  public function testHighLow() {
    $this->drupalLogin($this->user_high);
    $this->assertTrue(
      $this->assignRoleToUser($this->rid_low, $this->user_low),  // could be any user
      t('%role1 role can assign %role2 role.', array(
        '%role1' => 'High',
        '%role2' => 'low'
      )),
      t('Role Delegation')
    );
  }

  /**
   * Check that high role can't assign high role.
   */
  public function testHighHigh() {
    $this->drupalLogin($this->user_high);
// Just check that no option is presented to the user.
    $this->assertFalse(
      $this->assignRoleToUser($this->rid_high, $this->user_high),  // could be any user
      t("%role1 role can't assign %role2 role.", array(
        '%role1' => 'High',
        '%role2' => 'high'
      )),
      t('Role Delegation')
    );
  }

  /**
   * Check that roles can't be assigned by forgery.
   */
  public function testRoleForgery() {
    $this->drupalLogin($this->user_high);

// Have the nefarious high user forge an option to assign the high role...
    $this->drupalGet("user/{$this->user_low->id()}/edit");
    $name = "roles_change[{$this->rid_low}]";
    $input = $this->xpath("//input[@name='$name']");
    $dome = dom_import_simplexml($input[0]);
    $dome->setAttribute('value', $this->rid_high);

// ... then submit the form, and check that he didn't get the role.
    $this->drupalPostForm(NULL, array($name => TRUE), t('Save'));
    $this->assertRaw(
      t('An illegal choice has been detected. Please contact the site administrator.'),
      t('Role assignment forgery is blocked.') . ' (#1)',
      t('Role Delegation')
    );
    $this->assertFieldByName(
      $name,
      $this->rid_low,
      t('Role assignment forgery is blocked.') . ' (#2)',
      t('Role Delegation')
    );
  }
}
