<?php

/**
 * @file
 * Contains \Drupal\article_update\Form\ArticlUpdate.
 */

namespace Drupal\article_update\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Link;
use Psr\Log\LoggerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormStateInterFace;

class ArticleUpdate extends FormBase {

    /**
   * {@inheritdoc}
   */

    public function getFormId() {
        return 'article_update_settings';
    }

    public function buildForm(array $form, FormStateInterFace $form_state){

        $values = $form_state->getValues();

        $node_types = ['article'];
        $node_query = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->getQuery()
        ->condition('type', $node_types);

        $nids = $node_query->execute();
        $node_data=[0 => 'Choose node'];
        $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
        $data = [0 => 'Choose article'];

        foreach ($nodes as $node){
            $data[$node->id()]= $node->getTitle();
        };

        $node_data = $data;
        $node_status = [0 => 'unpublished', 1 => 'published'];
        $node_sticky = [0 => 'unsticky', 1 => 'sticky'];

        // Case Ajax 1
        $form['node_article_v2'] = [
            '#type' => 'select',
            '#title' => $this->t('Case AJAX 1'),
            '#options' => $node_data,
            '#ajax' => [
                'wrapper' => 'edit-output_2',
                'callback' => '::myAjaxCallback_2',
            ],
        ];

        $form['node_ajax_1'] = [
            '#type' => 'container',
            '#title' => $this->t('This is node title'),
            '#open' => TRUE,
            '#attributes' => ['id' => 'edit-output_2'],
        ];
        
        // ONLY LOADED IN AJAX RESPONSE OR IF FORM STATE VALUES POPULATED.
        if (!empty($values) && !empty($values['node_article_v2']) ) {
            $nid = $values['node_article_v2'];
            $article_select = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
            // debug report in site
//            \Drupal::logger('module_name_values')->notice('<pre><code>' . print_r($values, TRUE) . '</code></pre>' );
            $status_ajax_1 = $article_select->isPublished() ? 1 : 0 ;
            $sticky_ajax_1 = $article_select->isSticky() ? 1 : 0 ;

            $form['node_ajax_1']['status'] = [
                '#type' => 'select',
                '#title' => $this->t('Status'),
                '#options' => $node_status,
                '#value' => $status_ajax_1,
            ];

            $form['node_ajax_1']['sticky'] = [
                '#type' => 'select',
                '#title' => $this->t('Sticky'),
                '#options' => $node_sticky,
                '#value' => $sticky_ajax_1,
            ];
        }

        // Case Ajax 2
        $form['node_article'] = [
            '#type' => 'select',
            '#title' => $this->t('Case AJax2'),
            '#options' => $node_data,
            '#ajax' => [
                'wrapper' => 'edit-output',
                'callback' => '::myAjaxCallback',
            ],
        ];


        $form['node'] = [
            '#type' => 'container',
            '#title' => $this->t('This is node title 1 '),
            '#open' => TRUE,
            '#attributes' => ['id' => 'edit-output'],
        ];

        $form['node']['status_ajax_2'] = [
            '#type' => 'select',
            '#title' => $this->t('Status'),
            '#options' => $node_status,
            '#default_value' => 0,
        ];

        $form['node']['sticky_ajax_2'] = [
            '#type' => 'select',
            '#title' => $this->t('Sticky'),
            '#options' => $node_sticky,
            '#default_value' => 0,
        ];

        // submit form
        $form['update'] = [
            '#type' => 'submit',
            '#value' => $this->t('Update'),
        ];


        $form['delete'] = [
            '#type' => 'submit',
            '#value' => t('Delete'),
        ];

        return $form;
    }

    public function myAjaxCallback(array &$form, FormStateInterface $form_state)
    {
        if ($selectedValue = $form_state->getValue('node_article')) {
            // Get the text of the selected option.
            $article_select = \Drupal::entityTypeManager()->getStorage('node')->load($selectedValue);
//            dump($article_select);
            $status_ajax_2 = $article_select->isPublished() ? 1 : 0;
            $sticky_ajax_2 = $article_select->isSticky() ? 1 : 0;
            // Place the text of the selected option in our textfield.
            $form['node']['status_ajax_2']['#value'] = $status_ajax_2;
            $form['node']['sticky_ajax_2']['#value'] = $sticky_ajax_2;
        }

        return $form['node'];
    }

    public function myAjaxCallback_2(array $form, FormStateInterface $form_state) {
        return $form['node_ajax_1'];
    }


    public function submitForm(array &$form, FormStateInterFace $form_state){

        $submitButton = $form_state->getTriggeringElement();
//        dump($submitButton['#id']);
        if ($submitButton['#id'] == 'edit-update'){
            $selectedValue = $form_state->getValue('node_article_v2');
//        $status = $form_state->getValue(['test','status']);
//        \Drupal::messenger()->addMessage($form['node_article_v2']['#options'][$selectedValue]);
            $test =$form_state->getValues();
//        $status = $test['status'];
            $status_ajax_1 = $form_state->getUserInput()['status'];
            $sticky_ajax_1 = $form_state->getUserInput()['sticky'];

//        //Case Ajax 1 submit form
            $node = \Drupal\node\Entity\Node::load($form_state->getValue('node_article_v2'));
            if (!empty($status_ajax_1) && $status_ajax_1 == 1){
                $node->setPublished(TRUE);
            }else{$node->setUnpublished();}

            if (!empty($sticky_ajax_1) && $sticky_ajax_1 == 1){
                $node->setSticky(TRUE);
            }else{$node->setSticky(FALSE);}
            $node->save();

            \Drupal::messenger()->addMessage($this->t('Update success!'));

        } if($submitButton['#id'] == 'edit-delete') {
            $node_delete = \Drupal::entityTypeManager()->getStorage('node')->load($form_state->getValue('node_article'));
            if ($node_delete) {
                $node_delete->delete();
            }
            \Drupal::messenger()->addMessage($this->t('Delete success!'));

        }
    }
}