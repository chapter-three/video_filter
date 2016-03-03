<?php
/**
 * @file
 * Contains \Drupal\video_filter\Plugin\VideoFilter\Vimeo.
 */

namespace Drupal\video_filter\Plugin\VideoFilter;

use Drupal\video_filter\VideoFilterBase;

/**
 * Provides Vimeo codec for Video Filter
 *
 * @VideoFilter(
 *   id = "vimeo",
 *   name = @Translation("Vimeo"),
 *   example_url = "https://vimeo.com/25290459",
 *   regexp = {
 *     "/vimeo\.com\/([0-9]+)/",
 *   },
 *   ratio = "16 / 9",
 *   control_bar_height = 0
 * )
 */
class Vimeo extends VideoFilterBase {

  /**
   * {@inheritdoc}
   */
  public function html5($video) {
    return [
      'url' => 'http://player.vimeo.com/video/' . $video['codec']['matches'][1] . ($video['autoplay'] ? '?autoplay=1' : ''),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function flash($video) {
    return [
      'url' => 'http://www.vimeo.com/moogaloop.swf?clip_id=' . $video['codec']['matches'][1] . '&amp;server=www.vimeo.com&amp;fullscreen=1&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;autoplay=' . $video['autoplay'],
    ];
  }

}
