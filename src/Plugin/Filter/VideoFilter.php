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
      $supported = [];
      $manager = $this->plugin_manager;
      $plugins = $manager->getDefinitions();
      foreach ($plugins as $codec) {
        $codec = $manager->createInstance($codec['id']);
        // Get plugin/codec usage instructions.
        $instructions = $codec->instructions();
        $supported[] = '<strong>' . $codec->getName() . '</strong>';
        if (!empty($instructions)) {
          $tips[] = $instructions;
        }
      }
      return $this->t('
        <p><strong>Video Filter</strong></p>
        <p>You may insert videos from popular video sites by using a simple tag <code>[video:URL]</code>.</p>
        <p>Examples:</p>
        <ul>
          <li>Single video:<br /><code>[video:http://www.youtube.com/watch?v=uN1qUeId]</code></li>
          <li>Random video out of multiple:<br /><code>[video:http://www.youtube.com/watch?v=uN1qUeId1,http://www.youtube.com/watch?v=uN1qUeId2]</code></li>
          <li>Override default autoplay setting: <code>[video:http://www.youtube.com/watch?v=uN1qUeId autoplay:1]</code></li>
          <li>Override default width and height:<br /><code>[video:http://www.youtube.com/watch?v=uN1qUeId width:X height:Y]</code></li>
          <li>Override default aspect ratio:<br /><code>[video:http://www.youtube.com/watch?v=uN1qUeId ratio:4/3]</code></li>
          <li>Align the video:<br /><code>[video:http://www.youtube.com/watch?v=uN1qUeId align:right]</code></li>
        </ul>
        <p>Supported sites: !codecs.</p>
        <p><strong>Special instructions:</strong></p>
        <p><em>Some codecs need special input. You\'ll find those instructions here.</em></p>
        <ul>!instructions</ul>', [
          '!codecs' => implode(', ', $supported),
          '!instructions' => implode('', $tips),
        ]
      );
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
