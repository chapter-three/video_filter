<?php
/**
 * @file
 * Contains Drupal\video_filter\Plugin\Filter\VideoFilterVideoFilter
 */

namespace Drupal\video_filter\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Render Video Filter.
 *
 * @Filter(
 *   id = "video_filter",
 *   title = @Translation("Video Filter"),
 *   description = @Translation("Substitutes [video:URL] with embedded HTML."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   settings = {
 *     width = 400,
 *     height = 400,
 *     autoplay = FALSE,
 *     related = FALSE,
 *     html5 = TRUE,
 *   }
 * )
 */
class VideoFilter extends FilterBase {

  /**
   * Implements __construct().
   */
  public function __construct() {
    parent::__construct();

  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    return new FilterProcessResult( $text );
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return $this->t('Display LONG text (get instruction() method from plugins)');
    }
    else {
      return $this->t('You may insert videos with [video:URL]');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default width setting'),
      '#default_value' => $this->settings['width'],
      '#maxlength' => 4,
    ];
    $form['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default height setting'),
      '#default_value' => $this->settings['height'],
      '#maxlength' => 4,
    ];
    $form['autoplay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Default autoplay setting'),
      '#default_value' => $this->settings['autoplay'],
      '#description' => $this->t('Not all video formats support this setting.'),
    ];
    $form['related'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Related videos setting'),
      '#default_value' => $this->settings['related'],
      '#description' => $this->t('Show "related videos"? Not all video formats support this setting.'),
    ];
    $form['html5'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use HTML5'),
      '#default_value' => $this->settings['html5'],
      '#description' => $this->t('Use HTML5 if the codec provides it. Makes your videos more device agnostic.'),
    ];
    return $form;
  }

}
