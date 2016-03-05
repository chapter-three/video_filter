<?php
/**
 * @file
 * Contains \Drupal\video_filter\Plugin\VideoFilter\Youtube.
 */

namespace Drupal\video_filter\Plugin\VideoFilter;

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
 *   ratio = "16/9",
 *   control_bar_height = 25
 * )
 */
class Youtube extends VideoFilterBase {

  /**
   * {@inheritdoc}
   */
  public function iframe($video) {
    $attributes = [
      'rel' => !empty($video['related']) ? 'rel=1' : 'rel=0',
      'autoplay' => !empty($video['autoplay']) ? 'autoplay=1' : 'autoplay=0',
      'wmode' => 'wmode=opaque',
    ];
    // YouTube Playlist.
    // Example URL: https://www.youtube.com/watch?v=YQHsXMglC9A&list=PLFgquLnL59alCl_2TQvOiD5Vgm1hCaGSI
    if (preg_match_all('/youtube\.com\/watch\?v=(.*)list=([a-z0-9\-_]+)/i', $video['source'], $matches)) {
      if (!empty($matches[2][0])) {
        $attributes['list'] = 'list=' . $matches[2][0];
      }
    }
    return [
      'src' => 'http://www.youtube.com/embed/' . $video['codec']['matches'][1] . '?' . implode('&amp;', $attributes),
      'properties' => [
        'allowfullscreen' => 'true',
      ],
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
      'src' => 'http://www.youtube.com/v/' . $video['codec']['matches'][1] . '?' . implode('&amp;', $attributes),
      'params' => [
        'wmode' => 'opaque',
      ],
    ];
  }

}
