<?php

/**
 * @file
 * Contains \Drupal\role_delegation\Controller\RoleDelegationController.
 */

namespace Drupal\role_delegation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for contact routes.
 */
class RoleDelegationController extends ControllerBase {

  /**
   * Form constructor for the user role settings form.
   *
   * @param \Drupal\user\UserInterface $user
   *   The account for which a personal contact form should be generated.
   *
   * @return array
   *   The personal role settings form as render array as expected by drupal_render().
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Exception is thrown when user tries to access a contact form for a
   *   user who does not have an email address configured.
   */
  public function editForm(UserInterface $user) {
    return $this->formBuilder()->getForm('Drupal\role_delegation\Form\RoleDelegationSettingsForm', $user);
  }

}
