<?php
/**
 * @file
 * Provides Drupal\video_filter\VideoFilterBase.
 */

namespace Drupal\video_filter;

use Drupal\Component\Plugin\PluginBase;

class VideoFilterBase extends PluginBase implements VideoFilterInterface {

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
    return $this->pluginDefinition['ratio'];
  }

  /**
   * Get video player control bar height.
   */
  public function getControlBarHeight() {
    return $this->pluginDefinition['control_bar_height'];
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
  public function html5($video) {
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

}
