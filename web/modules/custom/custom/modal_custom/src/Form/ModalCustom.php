<?php
/**
 * @file
 */
namespace Drupal\modal_custom\Form;

use Drupal\Core\Form\FormBase;

use Drupal\Core\Form\FormStateInterface;

class ModalCustom extends FormBase {
  /**
   * {@inheritdoc}
   */
   
  public function getFormId(){
    return 'modal_custom';
  }


  public function buildForm(array $form, FormStateInterface $form_state) {

    if (!\Drupal::currentUser()->hasPermission('access secret notes')) {
      return t('You dont have permission in this page');
    }

    $form['title_modal'] = [
      '#type' => 'label',
      '#title' => 'Just checking',
    ];

    $avc ='ABCDFEEF';

    $form['title_modal_2'] = [
      '#type' => 'label',
      '#title' => 'We saw that you clicked prefer not to answer the more information you provide, the higher likelihood of having a productive conversation with your doctor.',
    ];

    $form['title_modal_3'] = [
      '#type' => 'label',
      '#title' => 'Do you wish to proceed ?',
    ];
    
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
    ];

    $form['continute'] = [
      '#type' => 'submit',
      '#value' => $this->t('Continute'),
    ];
    


    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    
  }



  public function submitForm(array &$form, FormStateInterface $form_state) {

  
    \Drupal::service('messenger')->addMessage("Why won't this message show?");
    $tempstore = \Drupal::service('tempstore.private');
    
    $store = $tempstore->get('modal_custom');

    $values ='123';

    $store->set('key_name', $values);

    $form_state->setRedirect('pfizer_question.subscribe_form');


  }

   
}