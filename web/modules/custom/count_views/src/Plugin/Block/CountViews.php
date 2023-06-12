<?php

namespace Drupal\count_views\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Database\Database;
use Drupal\node\NodeInterface;
use Drupal\Core\Cache\Cache;
/**
 * Provides a 'Count Views' Block.
 *
 * @Block(
 *   id = "count_views",
 *   admin_label = @Translation("Count Views"),
 *   category = @Translation("Count Views Node Process"),
 * )
 */
class CountViews extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $config = $this->getConfiguration();

        $database = \Drupal::database();

        //get day
//        $time_value = \Drupal::time()->getCurrentTime();
//        $timeFormat = \Drupal::service('date.formatter')->format($time_value, 'custom', "Y-m-d");
        $today = (new \DateTime('today'))->format('Y-m-d');
        $tomorrow = (new \DateTime('tomorrow'))->format('Y-m-d');

        //get id node form url and convert
        $possible_parameters = \Drupal::routeMatch()->getParameters();
        $current_path = \Drupal::service('path.current')->getPath();
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node instanceof NodeInterface) {
            $node_id = $node->id();
            $query = $database->select('history_process', 'hp')
                ->condition('nid', $node_id)
                ->fields('hp');

            $count = $query->countQuery()->execute()->fetchField();
            $today_views = $query->condition('current_date', [$today, $tomorrow], 'BETWEEN')->execute()->fetchAll();
            $count_today_views = count($today_views);

            $query2 = $database->select('history_process', 'hp')
                ->condition('nid', $node_id)
                ->fields('hp');
            $last_viewed = $query2->orderBy('id','desc')->range(1,1)->execute()->fetchObject();


//            $last_viewed = $query->orderBy('id','desc')->range(1,1)->execute()->fetchAll();
//            $last_viewed = end($last_viewed);
//            $items = [
//                'total_views' => $count - 1,
//                'total_views_day' => $count_today_views,
//                'name' => $last_viewed->name,
//                'time_last_process' => $last_viewed->current_date,
//            ];
        }

        return [
            '#theme' => 'count_views',
            '#total_views' => $count - 1,
            '#total_views_day' => $count_today_views-1,
            '#name' => $last_viewed->name,
            '#time_last_process' => $last_viewed->current_date,

        ];

      }
    public function getCacheTags() {
        //With this when your node change your block will rebuild
        if ($node = \Drupal::routeMatch()->getParameter('node')) {
            //if there is node add its cachetag
            $nid=$node->id();
            return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
        } else {
            //Return default tags instead.
            return parent::getCacheTags();
        }
    }

    public function getCacheMaxAge() {
        return 0;
    }

}