<?php

namespace Drupal\cron_mail\Plugin\QueueWorker;

/**
 * A report worker.
 *
 * @QueueWorker(
 *   id = "cron_mail_queue_1",
 *   title = @Translation("First worker in cron_mail"),
 *   cron = {"time" = 1}
 * )
 *
 * QueueWorkers are new in Drupal 8. They define a queue, which in this case
 * is identified as cron_mail_queue_1 and contain a process that operates on
 * all the data given to the queue.
 *
 * @see queue_example.module
 */
class ReportWorkerOne extends ReportWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $this->reportWork(1, $data);
  }

}
