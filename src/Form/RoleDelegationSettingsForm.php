<?php
/**
 * @file
 * Contains \Drupal\role_delegation\Form\RoleDelegationSettingsForm.
 */
namespace Drupal\role_delegation\Form;

use Drupal\Core\Config\Config;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\role_delegation\RoleDelegationHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;



/**
 * Configure book settings for this site.
 */
class RoleDelegationSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'role_delegation_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, UserInterface $user = NULL) {
    $form = RoleDelegationHelper::addRoleDelegationElement($form, $user);

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if(!empty($form_state->getValue('account')->uid)) {
      $account = '';
      $uid_list = $form_state->getValue('account')->uid->getValue();
      foreach($uid_list as $uid) {
        $account = user_load($uid['value']);
      }
      foreach($form_state->getValue('roles_change') as $rid => $value) {
        $account->addRole($rid);
        if($value === 0) {
          $account->removeRole($rid);
        }
        $account->save();
        drupal_set_message(t('The roles have been updated.'), 'status');
      }
    }
  }

}
