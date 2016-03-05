<?php
/**
 * @file
 * Contains \Drupal\video_filter\Plugin\VideoFilter\Instagram.
 */

namespace Drupal\video_filter\Plugin\VideoFilter;

use Drupal\video_filter\VideoFilterBase;

/**
 * Provides Instagram codec for Video Filter
 *
 * @VideoFilter(
 *   id = "instagram",
 *   name = @Translation("Instagram"),
 *   example_url = "https://www.instagram.com/p/BB-VhgbENtG/",
 *   regexp = {
 *     "/instagram\.com\/p\/([a-z0-9\-_]+)/i",
 *     "/instagr.am\/p\/([a-z0-9\-_]+)/i",
 *   },
 *   ratio = "612/710",
 * )
 */
class Instagram extends VideoFilterBase {

  /**
   * {@inheritdoc}
   */
  public function iframe($video) {
    return [
      'src' => '//www.instagram.com/p/' . $video['codec']['matches'][1] . '/embed',
    ];
  }

}
