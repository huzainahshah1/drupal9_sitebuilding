<?php

namespace Drupal\my_test_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;



class MyTestCoreController extends ControllerBase {

  /**
   * Returns a render-able array for a test page.
   */
  public function content() {
    return [
      '#theme' => 'my_template',
      '#some_custom_text' => $this->t('some custom text'),
      '#title_of_vehicle' => $this->t('title of vehicle'),
      '#auhors_name' => $this->t('authors name'),
      '#custom_text'=>$this->t('hey'),
    ];

  }


  public function access(AccountInterface $account) {
    // Check permissions and combine that with any custom access checking needed. Pass forward
    // parameters from the route and/or request as needed.

    $config = $this->config('ViewMyVehiclePageAccess.adminsettings')->get('ViewMyVehiclePageAccess')->get('checkboxes_section');
    $roles = $account->getRoles();
    $role_ids = [];
    foreach ($roles as $role) {
      $role_ids[] = $role->id();
    }
    if(array_intersect($config,$role_ids)) {
      return AccessResult::allowed();
    }
      else{
        return AccessResult::forbidden();
      }


    //$roles = $account->getRoles();
    //return AccessResult::allowedIf(in_array('vehicle_admin', $roles));
  }




}
