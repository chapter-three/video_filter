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
    if (preg_match_all('/\[video(\:(.+))?( .+)?\]/isU', $text, $matches_code)) {
      foreach ($matches_code[0] as $ci => $code) {
        $video = array(
          'source' => $matches_code[2][$ci],
          'autoplay' => $this->settings['autoplay'],
          'related' => $this->settings['related'],
        );

        // Pick random out of multiple sources separated by comma (,).
        if (strstr($video['source'], ',')) {
          $sources          = explode(',', $video['source']);
          $random           = array_rand($sources, 1);
          $video['source']  = $sources[$random];
        }

        // Load all codecs.
        $manager = $this->plugin_manager;
        $plugins = $manager->getDefinitions();

        // Find codec.
        foreach ($plugins as $id => $plugin) {
          $plugin = $manager->createInstance($plugin['id']);

          $codec_name = $plugin->getName();
          $regexp = $plugin->getRegexp();

          $codec = [];
          if (!is_array($regexp)) {
            $codec['regexp'][] = $regexp;
          }
          else {
            $codec['regexp'] = $regexp;
          }

          // Try different regular expressions.
          foreach ($codec['regexp'] as $delta => $regexp) {
            if (preg_match($regexp, $video['source'], $matches)) {
              $video['codec'] = $codec;
              $video['codec']['delta'] = $delta;
              $video['codec']['ratio'] = $plugin->getRatio();
              $video['codec']['control_bar_height'] = $plugin->getControlBarHeight();
              $video['codec']['matches'] = $matches;
              // Used in theme function:
              $video['codec']['codec_name'] = $codec_name;
              break 2;
            }
          }
        }

        // Codec found.
        if (isset($video['codec'])) {
          // Override default attributes.
          if ($matches_code[3][$ci] && preg_match_all('/\s+([a-zA-Z_]+)\:(\s+)?([0-9a-zA-Z\/]+)/i', $matches_code[3][$ci], $matches_attributes)) {
            foreach ($matches_attributes[0] as $ai => $attribute) {
              $video[$matches_attributes[1][$ai]] = $matches_attributes[3][$ai];
            }
          }

          // Use configured ratio if present, otherwise use that from the codec,
          // if set. Fall back to 1.
          $ratio = 1;
          if (isset($video['ratio']) && preg_match('/(\d+)\/(\d+)/', $video['ratio'], $tratio)) {
            // Validate given ratio parameter.
            $ratio = $tratio[1] / $tratio[2];
          }
          elseif (isset($video['codec']['ratio'])) {
            $ratio = $video['codec']['ratio'];
          }

          // Sets video width & height after any user input has been parsed.
          // First, check if user has set a width.
          if (isset($video['width']) && !isset($video['height'])) {
            $video['height'] = $this->settings['height'];
          }
          // Else, if user has set height.
          elseif (isset($video['height']) && !isset($video['width'])) {
            $video['width'] = $video['height'] * $ratio;
          }
          // Maybe both?
          elseif (isset($video['height']) && isset($video['width'])) {
            $video['width'] = $video['width'];
            $video['height'] = $video['height'];
          }
          // Fall back to defaults.
          elseif (!isset($video['height']) && !isset($video['width'])) {
            $video['width'] = $this->settings['width'] != '' ? $this->settings['width'] : 400;
            $video['height'] = $this->settings['height'] != '' ? $this->settings['height'] : 400;
          }

          // Default value for control bar height.
          $control_bar_height = 0;
          if (isset($video['control_bar_height'])) {
            // Respect control_bar_height option if present.
            $control_bar_height = $video['control_bar_height'];
          }
          elseif (isset($video['codec']['control_bar_height'])) {
            // Respect setting provided by codec otherwise.
            $control_bar_height = $video['codec']['control_bar_height'];
          }

          // Resize to fit within width and height repecting aspect ratio.
          if ($ratio) {
            $scale_factor = min(array(
              ($video['height'] - $control_bar_height),
              $video['width'] / $ratio,
            ));
            $video['height'] = round($scale_factor + $control_bar_height);
            $video['width'] = round($scale_factor * $ratio);
          }

          $video['autoplay'] = (bool) $video['autoplay'];
          $video['align'] = (isset($video['align']) && in_array($video['align'], [
            'left',
            'right',
            'center',
          ])) ? $video['align'] : NULL;

          // Let modules have final say on video parameters.
          // drupal_alter('video_filter_video', $video);

          $html5 = $plugin->html5($video);
          $flash = $plugin->flash($video);
          $html = $plugin->html($video);

          if (!empty($html5) && $this->settings['html5'] == TRUE) {
            $video['url'] = !empty($html5['url']) ? $html5['url'] : '';
            $element = [
              '#theme' => 'video_filter_iframe',
              '#video' => $video,
              '#params' => !empty($html5['params']) ? $html5['params'] : []
            ];
            $replacement = drupal_render($element);
          }
          elseif (!empty($flash)) {
            $video['url'] = !empty($flash['url']) ? $flash['url'] : '';
            $element = [
              '#theme' => 'video_filter_flash',
              '#video' => $video,
              '#params' => !empty($flash['params']) ? $flash['params'] : []
            ];
            $replacement = drupal_render($element);
          }
          elseif (!empty($html)) {
            $element = [
              '#theme' => 'video_filter_html',
              '#video' => $html,
            ];
            $replacement = drupal_render($element);
          }
          else {
            // Invalid callback.
            $replacement = '<!-- VIDEO FILTER - INVALID CALLBACK IN: ' . $pattern . ' -->';
          }
        }
        // Invalid format.
        else {
          $replacement = '<!-- VIDEO FILTER - INVALID CODEC IN: ' . $code . ' -->';
        }

        $text = str_replace($code, $replacement, $text);
      }
    }
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
      foreach ($plugins as $plugin) {
        $plugin = $manager->createInstance($plugin['id']);
        // Get plugin/codec usage instructions.
        $instructions = $plugin->instructions();
        $supported[] = '<strong>' . $plugin->getName() . '</strong>';
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
