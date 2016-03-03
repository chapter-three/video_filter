<?php
/**
 * @file
 * Contains \Drupal\video_filter\Plugin\VideoFilter\BlipTV.
 */

namespace Drupal\video_filter\Plugin\VideoFilter;

use Drupal\video_filter\VideoFilterBase;

/**
 * Provides BlipTV codec for Video Filter
 *
 * @VideoFilter(
 *   id = "blip_tv",
 *   name = @Translation("Blip.tv"),
 *   example_url = "http://blip.tv/file/123456",
 *   regexp = {},
 *   ratio = "16 / 9",
 *   control_bar_height = 30,
 * )
 */
class BlipTV extends VideoFilterBase {

  /**
   * {@inheritdoc}
   */
  public function getRegexp() {
  	// If regex generates errors due to a quote in the regexp
  	// Implement this method in your plugin.
    return [
      '@blip\.tv/rss/flash/([^"\&\?/]+)@i',
      '@blip\.tv/file/view/([^"\&\?/]+)@i',
      '@blip\.tv/file/([^"\&\?/]+)@i',
      '@blip\.tv/play/([^"\&\?/]+)@i',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function flash($video) {
    return [
      'url' => 'http://www.archive.org/embed/' . $video['codec']['matches'][1],
    ];
  }

}
