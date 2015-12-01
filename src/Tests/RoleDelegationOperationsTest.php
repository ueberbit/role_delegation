<?php

/**
 * @file
 * Contains \Drupal\role_delegation\Tests\RoleDelegationOperationsTest.
 */

namespace Drupal\role_delegation\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\user\Entity\Role;
/**
 * Functional tests for operations.
 *
 * @group role_delegation
 */
class RoleDelegationOperationsTest extends RoleDelegationTest {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
}

  public static function getInfo() {
    return array(
      'name' => t('Operations'),
      'description' => t('Check that role assignment bulk operations are available and work as intended.'),
      'group' => t('Role Delegation'),
    );
  }

  /**
   * Check that the right combination of Add and Remove role
   * operations is present in the user bulk update form.
   */
  public function testOperationsExist() {

    $this->drupalLogin($this->user_high);
    $this->drupalGet('admin/people');

    $this->assertFieldByXPath(
      '//select[@name="operation"]//option',
      "role_delegation_add_role-{$this->rid_low}",
      t("!user user can use Add !role role operation.", array('!user' => 'High', '!role' => 'low')),
      t('Role Delegation')
    );
    $this->assertFieldByXPath(
      '//select[@name="operation"]//option',
      "role_delegation_remove_role-{$this->rid_low}",
      t("!user user can use Remove !role role operation.", array('!user' => 'High', '!role' => 'low')),
      t('Role Delegation')
    );
    $this->assertNoFieldByXPath(
      '//select[@name="operation"]//option',
      "role_delegation_add_role-{$this->rid_high}",
      t("!user user can't use Add !role role operation.", array('!user' => 'High', '!role' => 'high')),
      t('Role Delegation')
    );
    $this->assertNoFieldByXPath(
      '//select[@name="operation"]//option',
      "role_delegation_remove_role-{$this->rid_high}",
      t("!user user can't use Remove !role role operation.", array('!user' => 'High', '!role' => 'high')),
      t('Role Delegation')
    );
  }

  /**
   * Check that Add and Remove role operations work as intended.
   */
/*  public function testOperationsWork() {

    $uids_to_test = array($this->user_high->uid, $this->user_low->uid);
    $edit = array();
    foreach ($uids_to_test as $uid) {
      $edit["accounts[$uid]"] = TRUE;
    }

    $this->drupalLogin($this->user_high);
    $this->drupalGet('admin/people');

    // Add low role
    $edit['operation'] = "role_delegation_add_role-{$this->rid_low}";
    $this->drupalPost(NULL, $edit, t('Update'));
    foreach ($uids_to_test as $uid) {
      $this->assertFieldByXPath(
        "//tbody/tr[$uid]/td[4]//li",
        'low',
        t('!user user assigned !role role to user !uid.',  array('!user' => 'High', '!role' => 'low', '!uid' => $uid)),
        t('Role Delegation')
      );
    }

    // Remove low role
    $edit['operation'] = "role_delegation_remove_role-{$this->rid_low}";
    $this->drupalPost(NULL, $edit, t('Update'));
    foreach ($uids_to_test as $uid) {
      $this->assertNoFieldByXPath(
        "//tbody/tr[$uid]/td[4]//li",
        'low',
        t('!user user removed !role role from user !uid.', array('!user' => 'High', '!role' => 'low', '!uid' => $uid)),
        t('Role Delegation')
      );
    }
  }*/

  /**
   * Check that operations can't be forged.
   */
/*  public function testOperationsForgery() {
    $this->drupalLogin($this->user_high);
    $this->drupalGet('admin/people');

    // Forge an operation to add the high role...
    $option = $this->xpath("//select[@name='operation']//option[@value='role_delegation_add_role-{$this->rid_low}']");
    if (count($option)==0) {
      return;
    }
    $dome = dom_import_simplexml($option[0]);
    $dome->setAttribute('value', "role_delegation_add_role-{$this->rid_high}");

    // ... then submit the form, and check that it wasn't granted.
    $edit = array(
      "accounts[{$this->user_low->uid}]" => TRUE,
      "operation" => "role_delegation_add_role-{$this->rid_high}",
    );
    $this->drupalPost(NULL, $edit, t('Update'));
    $this->assertRaw(
      t('An illegal choice has been detected. Please contact the site administrator.'),
      t('Role assignment forgery is blocked.') . ' (#1)',
      t('Role Delegation')
    );
    $this->assertNoFieldByXPath(
      "//tbody/tr[{$this->user_high->uid}]/td[4]//li",
      'high',
      t('Role assignment forgery is blocked.') . ' (#2)',
      t('Role Delegation')
    );
  }*/

}
