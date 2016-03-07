<?php

/**
 * @file
 * Contains \Drupal\video_filter\Form\VideoFilterDialog.
 */

namespace Drupal\video_filter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\UpdateBuildIdCommand;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\video_filter\VideoFilterCore;

/**
 * Provides Video Filter dialog for text editors.
 */
class VideoFilterDialog extends FormBase implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('module_handler'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'video_filter_dialog';
  }

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\filter\Entity\FilterFormat $filter_format
   *   The filter format for which this dialog corresponds.
   */
  public function buildForm(array $form, FormStateInterface $form_state, FilterFormat $filter_format = NULL) {
    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 'editor/drupal.editor.dialog';
    $form['#prefix'] = '<div id="video-filter-dialog-form">';
    $form['#suffix'] = '</div>';

    $form['url'] = [
      '#title'     => $this->t('Video URL'),
      '#type'      => 'textfield',
      '#maxlength' => 2048,
      '#ajax' => [
        'callback' => '::getPluginOptions',
        'event'    => 'change',
      ],
    ];

    $form['options'] = [];
    $vf = new VideoFilterCore();
    $plugins = $vf->loadPlugins();
    foreach ($plugins['plugins'] as $id => $plugin_info) {
      if (!empty($plugin_info['options'])) {
        $form['options'][$id] = [
          '#type'   => 'details',
          '#title'  => $this->t('Options'),
          '#open'   => TRUE,
          '#prefix' => '<div class="visually-hidden">',
          '#suffix' => '</div>',
        ];
        $form['options'][$id]['options'] = $plugin_info['options'];
      }
    }

    $form['align'] = [
      '#title'         => $this->t('Align (optional)'),
      '#type'          => 'select',
      '#default_value' => 'none',
      '#options' => [
        'none'   => $this->t('None'),
        'left'   => $this->t('Left'),
        'right'  => $this->t('Right'),
        'center' => $this->t('Center'),
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['save_modal'] = [
      '#type'   => 'submit',
      '#value'  => $this->t('Insert'),
      // No regular submit-handler. This form only works via JavaScript.
      '#submit' => [],
      '#ajax'   => [
        'callback' => '::submitForm',
        'event'    => 'click',
      ],
    ];

    // This is the element where we put generated code
    // By doing this we can generate [video:url]
    // in PHP instead of generating it in CKEditor JS plugin.
    $form['attributes']['code'] = [
      '#title'  => $this->t('Video Filter'),
      '#type'   => 'textfield',
      '#prefix' => '<div class="visually-hidden">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    // Generate shortcut/token code.
    $url = $form_state->getValue('url');
    $shortcode = '[video:';
    if ($url) {
      $shortcode .= $url;
      $input = $form_state->getUserInput();
      $vf = new VideoFilterCore();
      $plugin = $vf->loadPlugins($url);
      if (!empty($plugin['options'])) {
        foreach ($input['options'][$plugin['id']]['options'] as $key => $val) {
          if (!empty($val)) {
            $shortcode .= ' ' . $key  . ':' . $val;
          }
        }
      }
    }
    if ($form_state->getValue('align') && $form_state->getValue('align') != 'none') {
      $shortcode .= ' align:' . $form_state->getValue('align');
    }
    $shortcode .= ']';

    if ( !empty($shortcode) && !empty($url) ) {
      $form_state->setValue(['attributes', 'code'], $shortcode);
    }

    if ($form_state->getErrors()) {
      unset($form['#prefix'], $form['#suffix']);
      $form['status_messages'] = [
        '#type'   => 'status_messages',
        '#weight' => -10,
      ];
      $response->addCommand(new HtmlCommand('#video-filter-dialog-form', $form));
    }
    else {
      $response->addCommand(new EditorDialogSave($form_state->getValues()));
      $response->addCommand(new CloseModalDialogCommand());
    }

    return $response;
  }

  /**
   * Get plugin embedding options.
   */
  public function getPluginOptions(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    // Video URL.
    $url = $form_state->getValue('url');
    if (!empty($url)) {
      $vf = new VideoFilterCore();
      $plugin = $vf->loadPlugins($url);
      if (!empty($plugin['id'])) {
        $form['options'][$plugin['id']]['#prefix'] = '';
        $form['options'][$plugin['id']]['#suffix'] = '';
      }
    }
    $response->addCommand(new HtmlCommand('#video-filter-dialog-form', $form));
    return $response;
  }

}
