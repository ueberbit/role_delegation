<?php

/**
 * @file
 * Contains \Drupal\role_delegation\Tests\RoleDelegationEditingTest.
 */

namespace Drupal\role_delegation\Tests;

/**
 * Functional tests for editing roles.
 *
 * @group role_delegation
 */
class RoleDelegationEditingTest extends RoleDelegationTest {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  public static function getInfo() {
    return array(
      'name' => t('Role editing'),
      'description' => t('Check that role assignment permissions are updated correctly when roles are renamed or deleted.'),
      'group' => t('Role Delegation'),
    );
  }

  /**
   * Rename a role, and check that users that had permission to assign
   * the old role now have permission to assign the new one.
   */
  public function testRenameRole() {
    $this->drupalPostForm("admin/people/roles/manage/{$this->rid_low}", array('label' => 'new low'), t('Save'));
    $this->drupalGet('admin/people/permissions');
    $this->assertFieldChecked(
      "edit-{$this->rid_high}-assign-low-role",
      t('Permissions are updated when role is renamed.'),
      t('Role Delegation')
    );
  }

  /**
   * Delete a role, then create a new one with the same name.
   * Check that no users have permission to assign the new role.
   */
  public function testDeleteRole() {
    $this->drupalPostForm("admin/people/roles/manage/{$this->rid_low}/delete", NULL, t('Delete'));
    $this->drupalPostForm('admin/people/roles/add', array('label' => 'low', 'id' => 'low'), t('Save'));
    $this->drupalGet('admin/people/permissions');
    $this->assertNoFieldChecked(
      "edit-{$this->rid_high}-assign-low-role",
      t('Permissions are updated when role is deleted.'),
      t('Role Delegation')
    );
  }

}
