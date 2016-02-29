<?php
/**
 * @file
 * Contains \Drupal\video_filter\Plugin\VideoFilter\Youtube.
 */

namespace Drupal\video_filter\Plugin\Youtube;

use Drupal\video_filter\VideoFilterBase;

/**
 * Provides Youtube codec for Video Filter
 *
 * @VideoFilter(
 *   id = "youtube",
 *   name = @Translation("YouTube"),
 *   example_url = "https://www.youtube.com/watch?v=EQ1HKCYJM5U",
 *   regexp = {
 *     "/youtube\.com\/watch\?v=([a-z0-9\-_]+)/i",
 *     "/youtu.be\/([a-z0-9\-_]+)/i",
 *     "/youtube\.com\/v\/([a-z0-9\-_]+)/i",
 *   },
 *   ratio = "16 / 9",
 *   control_bar_height = 25
 * )
 */
class Youtube extends VideoFilterBase {

  /**
   * {@inheritdoc}
   */
  public function html5($video) {
    $attributes = [
      'rel' => !empty($video['related']) ? 'rel=1' : 'rel=0',
      'autoplay' => !empty($video['autoplay']) ? 'autoplay=1' : 'autoplay=0',
      'wmode' => 'wmode=opaque',
    ];
    return [
      'source' => 'http://www.youtube.com/embed/' . $video['codec']['matches'][1] . '?' . implode('&amp;', $attributes),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function flash($video) {
    $attributes = [
      'rel' => !empty($video['related']) ? 'rel=1' : 'rel=0',
      'autoplay' => !empty($video['autoplay']) ? 'autoplay=1' : 'autoplay=0',
      'fs' => 'fs=1',
    ];
    return [
      'source' => 'http://www.youtube.com/v/' . $video['codec']['matches'][1] . '?' . implode('&amp;', $attributes),
      'params' => [
        'wmode' => 'opaque',
      ],
    ];
  }

}
