<?php
/**
 * @file
 * Contains Drupal\video_filter\Plugin\Filter\VideoFilterVideoFilter
 */

namespace Drupal\video_filter\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Render Video Filter.
 *
 * @Filter(
 *   id = "video_filter",
 *   title = @Translation("Video Filter"),
 *   description = @Translation("Substitutes [video:URL] with embedded HTML."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   settings = {
 *     "width" = 400,
 *     "height" = 400,
 *     "autoplay" = FALSE,
 *     "related" = FALSE,
 *     "html5" = TRUE,
 *   }
 * )
 */
class VideoFilter extends FilterBase implements ContainerInjectionInterface {

  protected $plugin_manager;

  /**
   * Implements __construct().
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->plugin_manager = \Drupal::service('plugin.manager.video_filter');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('module_handler'));
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
      $tips = [];
      $manager = $this->plugin_manager;
      $plugins = $manager->getDefinitions();
      foreach ($plugins as $codec) {
        $instance = $manager->createInstance($codec['id']);
        // Get plugin/codec usage instructions.
        $instructions = $instance->instructions();
        if (!empty($instructions)) {
          $tips[] = $instructions;
        }
      }
      $build = [
        '#theme' => 'item_list',
        '#items' => $tips,
      ];
      if ($tips) {
        return drupal_render($build);
      }
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
      '#title' => $this->t('Autoplay video'),
      '#default_value' => $this->settings['autoplay'],
      '#description' => $this->t('Not all video formats support this setting.'),
    ];
    $form['related'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show delated videos'),
      '#default_value' => $this->settings['related'],
      '#description' => $this->t('Not all video formats support this setting.'),
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
