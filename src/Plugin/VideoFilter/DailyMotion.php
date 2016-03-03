<?php
/**
 * @file
 * Contains \Drupal\video_filter\Plugin\VideoFilter\DailyMotion.
 */

namespace Drupal\video_filter\Plugin\VideoFilter;

use Drupal\video_filter\VideoFilterBase;

/**
 * Provides DailyMotion codec for Video Filter
 *
 * @VideoFilter(
 *   id = "daily_motion",
 *   name = @Translation("DailyMotion"),
 *   example_url = "http://www.dailymotion.com/video/some_video_title",
 *   regexp = {
 *     "/dailymotion\.com\/video\/([a-z0-9\-_]+)/i",
 *   },
 *   ratio = "4 / 3",
 *   control_bar_height = 20,
 * )
 */
class DailyMotion extends VideoFilterBase {

  /**
   * {@inheritdoc}
   */
  public function flash($video) {
    return [
      'url' => 'http://www.dailymotion.com/swf/' . $video['codec']['matches'][1],
    ];
  }

}
