<?php
/**
 * @file
 * Contains \Drupal\video_filter\Plugin\VideoFilter\YouKu.
 */

namespace Drupal\video_filter\Plugin\YouKu;

use Drupal\video_filter\VideoFilterBase;

/**
 * Provides YouKu codec for Video Filter
 *
 * @VideoFilter(
 *   id = "youku",
 *   name = @Translation("YouKu"),
 *   example_url = "http://v.youku.com/v_show/id_XNzE1NTMyMDUy.html",
 *   regexp = {
 *     "/youku\.com\/v_show\/id_([a-z0-9\-_=]+)\.html/i",
 *     "/youku\.com\/player\.php\/sid\/([a-z0-9\-_]+)/i",
 *   },
 *   ratio = "16 / 9",
 *   control_bar_height = 50
 * )
 */
class YouKu extends VideoFilterBase {

  /**
   * {@inheritdoc}
   */
  public function html5($video) {
    return [
      'source' => 'http://player.youku.com/embed/' . $video['codec']['matches'][1] . '?' . implode('&amp;', $attributes),
    ];
  }

}
