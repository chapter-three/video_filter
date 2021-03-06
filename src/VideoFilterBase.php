<?php
/**
 * @file
 * Provides Drupal\video_filter\VideoFilterBase.
 */

namespace Drupal\video_filter;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class VideoFilterBase extends PluginBase implements VideoFilterInterface {

  use StringTranslationTrait;

  /**
   * Get plugin name.
   */
  public function getName() {
    return $this->pluginDefinition['name'];
  }

  /**
   * Get plugin example URL.
   */
  public function getExampleURL() {
    return $this->pluginDefinition['example_url'];
  }

  /**
   * Get plugin regexp.
   */
  public function getRegexp() {
    return $this->pluginDefinition['regexp'];
  }

  /**
   * Get video player ratio.
   */
  public function getRatio() {
    $ratio = !empty($this->pluginDefinition['ratio']) ? $this->pluginDefinition['ratio'] : '';
    if (!empty($ratio) && preg_match('/(\d+)\/(\d+)/', $ratio, $tratio)) {
      return $tratio[1] / $tratio[2];
    }
    return 1;
  }

  /**
   * Get video player control bar height.
   */
  public function getControlBarHeight() {
    return !empty($this->pluginDefinition['control_bar_height']) ? $this->pluginDefinition['control_bar_height'] : '';
  }

  /**
   * Get Video Filter coded usage instructions.
   */
  public function instructions() {
    // Return text of the instruction for the codec.
  }

  /**
   * HTML5 video (iframe).
   */
  public function iframe($video) {
    // Return HTML5 URL to the video
    // This URL will be passed int iframe.
  }

  /**
   * Flash video (flv)
   */
  public function flash($video) {
    // Usually video URL that will be played 
    // with the FLV player.
  }

  /**
   * HTML code of the video player.
   */
  public function html($video) {
    // Usually video URL that will be played 
    // with the FLV player.
  }

  /**
   * Embed options. (e.g. Autoplay, Width/Height)
   * Uses Drupal's Form API
   */
  public function options() {
    $form['width'] = [
      '#title' => $this->t('Width (optional)'),
      '#type' => 'textfield',
    ];
    $form['height'] = [
      '#title' => $this->t('Height (optional)'),
      '#type' => 'textfield',
    ];
    return $form;
  }

}
