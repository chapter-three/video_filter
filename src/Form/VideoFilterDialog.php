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
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\Core\Ajax\CloseModalDialogCommand;

/**
 * Provides Video Filter dialog for text editors.
 */
class VideoFilterDialog extends FormBase {

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
    // The default values are set directly from \Drupal::request()->request,
    // provided by the editor plugin opening the dialog.
    $user_input = $form_state->getUserInput();
    $input = isset($user_input['editor_object']) ? $user_input['editor_object'] : [];

    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 'editor/drupal.editor.dialog';
    $form['#prefix'] = '<div id="video-filter-dialog-form">';
    $form['#suffix'] = '</div>';

    $form['url'] = [
      '#title' => $this->t('Video URL'),
      '#type' => 'textfield',
      '#default_value' => isset($input['url']) ? $input['url'] : '',
      '#description' => $this->t('Only the following services supported: <strong>Youtube</strong>, <strong>Vimeo</strong>, <strong>YouKu</strong>.<br/>Simply copy the URL and paste it into this field.'),
      '#maxlength' => 2048,
    ];

    $form['width'] = [
      '#title' => $this->t('Width (optional)'),
      '#type' => 'textfield',
      '#default_value' => isset($input['width']) ? $input['width'] : '',
      '#description' => $this->t('The value should be in pixels.'),
      '#maxlength' => 4,
    ];

    $form['height'] = [
      '#title' => $this->t('Height (optional)'),
      '#type' => 'textfield',
      '#default_value' => isset($input['height']) ? $input['height'] : '',
      '#description' => $this->t('The value should be in pixels.'),
      '#maxlength' => 4,
    ];

    $form['align'] = [
      '#title' => $this->t('Align (optional)'),
      '#type' => 'select',
      '#default_value' => isset($input['align']) ? $input['align'] : 'none',
      '#options' => [
        'none' => $this->t('None'),
        'left' => $this->t('Left'),
        'right' => $this->t('Right'),
        'center' => $this->t('Center'),
      ],
    ];

    $form['autoplay'] = [
      '#title' => $this->t('Autoplay (optional)'),
      '#type' => 'checkbox',
      '#default_value' => !empty($input['autoplay']) ? TRUE : FALSE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['save_modal'] = [
      '#type' => 'submit',
      '#value' => $this->t('Insert'),
      // No regular submit-handler. This form only works via JavaScript.
      '#submit' => [],
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'click',
      ],
    ];

    // This is the element where we put generated code
    // By doing this we can generate [video:url]
    // in PHP instead of generating it in CKEditor JS plugin.
    $form['attributes']['code'] = [
      '#title' => $this->t('Video Filter'),
      '#type' => 'textfield',
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

    //$shortcode = $ev->shortcode();
    // Generate shortcode here and pass it to the form.
    if ( !empty($shortcode) ) {
      $form_state->setValue(['attributes', 'code'], $shortcode);
    }

    if ($form_state->getErrors()) {
      unset($form['#prefix'], $form['#suffix']);
      $form['status_messages'] = [
        '#type' => 'status_messages',
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

}
