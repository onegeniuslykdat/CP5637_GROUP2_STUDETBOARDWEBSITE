<?php

namespace Staatic\Vendor;

use stdClass;
abstract class WP_Background_Process extends WP_Async_Request
{
    protected $action = 'background_process';
    protected $start_time = 0;
    protected $cron_hook_identifier;
    protected $cron_interval_identifier;
    protected $allowed_batch_data_classes = \true;
    const STATUS_CANCELLED = 1;
    const STATUS_PAUSED = 2;
    public function __construct($allowed_batch_data_classes = \true)
    {
        parent::__construct();
        if (empty($allowed_batch_data_classes) && \false !== $allowed_batch_data_classes) {
            $allowed_batch_data_classes = \true;
        }
        if (!\is_bool($allowed_batch_data_classes) && !\is_array($allowed_batch_data_classes)) {
            $allowed_batch_data_classes = \true;
        }
        if (\true === $this->allowed_batch_data_classes || \true !== $allowed_batch_data_classes) {
            $this->allowed_batch_data_classes = $allowed_batch_data_classes;
        }
        $this->cron_hook_identifier = $this->identifier . '_cron';
        $this->cron_interval_identifier = $this->identifier . '_cron_interval';
        \add_action($this->cron_hook_identifier, array($this, 'handle_cron_healthcheck'));
        \add_filter('cron_schedules', array($this, 'schedule_cron_healthcheck'));
    }
    public function dispatch()
    {
        if ($this->is_processing()) {
            return \false;
        }
        $this->schedule_event();
        return parent::dispatch();
    }
    public function push_to_queue($data)
    {
        $this->data[] = $data;
        return $this;
    }
    public function save()
    {
        $key = $this->generate_key();
        if (!empty($this->data)) {
            \update_site_option($key, $this->data);
        }
        $this->data = array();
        return $this;
    }
    public function update($key, $data)
    {
        if (!empty($data)) {
            \update_site_option($key, $data);
        }
        return $this;
    }
    public function delete($key)
    {
        \delete_site_option($key);
        return $this;
    }
    public function delete_all()
    {
        $batches = $this->get_batches();
        foreach ($batches as $batch) {
            $this->delete($batch->key);
        }
        \delete_site_option($this->get_status_key());
        $this->cancelled();
    }
    public function cancel()
    {
        \update_site_option($this->get_status_key(), self::STATUS_CANCELLED);
        $this->dispatch();
    }
    public function is_cancelled()
    {
        $status = \get_site_option($this->get_status_key(), 0);
        return \absint($status) === self::STATUS_CANCELLED;
    }
    protected function cancelled()
    {
        \do_action($this->identifier . '_cancelled');
    }
    public function pause()
    {
        \update_site_option($this->get_status_key(), self::STATUS_PAUSED);
    }
    public function is_paused()
    {
        $status = \get_site_option($this->get_status_key(), 0);
        return \absint($status) === self::STATUS_PAUSED;
    }
    protected function paused()
    {
        \do_action($this->identifier . '_paused');
    }
    public function resume()
    {
        \delete_site_option($this->get_status_key());
        $this->schedule_event();
        $this->dispatch();
        $this->resumed();
    }
    protected function resumed()
    {
        \do_action($this->identifier . '_resumed');
    }
    public function is_queued()
    {
        return !$this->is_queue_empty();
    }
    public function is_active()
    {
        return $this->is_queued() || $this->is_processing() || $this->is_paused() || $this->is_cancelled();
    }
    protected function generate_key($length = 64, $key = 'batch')
    {
        $unique = \md5(\microtime() . \wp_rand());
        $prepend = $this->identifier . '_' . $key . '_';
        return \substr($prepend . $unique, 0, $length);
    }
    protected function get_status_key()
    {
        return $this->identifier . '_status';
    }
    public function maybe_handle()
    {
        \session_write_close();
        if ($this->is_processing()) {
            return $this->maybe_wp_die();
        }
        if ($this->is_cancelled()) {
            $this->clear_scheduled_event();
            $this->delete_all();
            return $this->maybe_wp_die();
        }
        if ($this->is_paused()) {
            $this->clear_scheduled_event();
            $this->paused();
            return $this->maybe_wp_die();
        }
        if ($this->is_queue_empty()) {
            return $this->maybe_wp_die();
        }
        \check_ajax_referer($this->identifier, 'nonce');
        $this->handle();
        return $this->maybe_wp_die();
    }
    protected function is_queue_empty()
    {
        return empty($this->get_batch());
    }
    protected function is_process_running()
    {
        return $this->is_processing();
    }
    public function is_processing()
    {
        if (\get_site_transient($this->identifier . '_process_lock')) {
            return \true;
        }
        return \false;
    }
    protected function lock_process()
    {
        $this->start_time = \time();
        $lock_duration = \property_exists($this, 'queue_lock_time') ? $this->queue_lock_time : 60;
        $lock_duration = \apply_filters($this->identifier . '_queue_lock_time', $lock_duration);
        \set_site_transient($this->identifier . '_process_lock', \microtime(), $lock_duration);
    }
    protected function unlock_process()
    {
        \delete_site_transient($this->identifier . '_process_lock');
        return $this;
    }
    protected function get_batch()
    {
        return \array_reduce($this->get_batches(1), static function ($carry, $batch) {
            return $batch;
        }, array());
    }
    public function get_batches($limit = 0)
    {
        global $wpdb;
        if (empty($limit) || !\is_int($limit)) {
            $limit = 0;
        }
        $table = $wpdb->options;
        $column = 'option_name';
        $key_column = 'option_id';
        $value_column = 'option_value';
        if (\is_multisite()) {
            $table = $wpdb->sitemeta;
            $column = 'meta_key';
            $key_column = 'meta_id';
            $value_column = 'meta_value';
        }
        $key = $wpdb->esc_like($this->identifier . '_batch_') . '%';
        $sql = '
			SELECT *
			FROM ' . $table . '
			WHERE ' . $column . ' LIKE %s
			ORDER BY ' . $key_column . ' ASC
			';
        $args = array($key);
        if (!empty($limit)) {
            $sql .= ' LIMIT %d';
            $args[] = $limit;
        }
        $items = $wpdb->get_results($wpdb->prepare($sql, $args));
        $batches = array();
        if (!empty($items)) {
            $allowed_classes = $this->allowed_batch_data_classes;
            $batches = \array_map(static function ($item) use ($column, $value_column, $allowed_classes) {
                $batch = new stdClass();
                $batch->key = $item->{$column};
                $batch->data = static::maybe_unserialize($item->{$value_column}, $allowed_classes);
                return $batch;
            }, $items);
        }
        return $batches;
    }
    protected function handle()
    {
        $this->lock_process();
        $throttle_seconds = \max(0, \apply_filters($this->identifier . '_seconds_between_batches', \apply_filters($this->prefix . '_seconds_between_batches', 0)));
        do {
            $batch = $this->get_batch();
            foreach ($batch->data as $key => $value) {
                $task = $this->task($value);
                if (\false !== $task) {
                    $batch->data[$key] = $task;
                } else {
                    unset($batch->data[$key]);
                }
                if (!empty($batch->data)) {
                    $this->update($batch->key, $batch->data);
                }
                \sleep($throttle_seconds);
                if ($this->time_exceeded() || $this->memory_exceeded() || $this->is_paused() || $this->is_cancelled()) {
                    break;
                }
            }
            if (empty($batch->data)) {
                $this->delete($batch->key);
            }
        } while (!$this->time_exceeded() && !$this->memory_exceeded() && !$this->is_queue_empty() && !$this->is_paused() && !$this->is_cancelled());
        $this->unlock_process();
        if (!$this->is_queue_empty()) {
            $this->dispatch();
        } else {
            $this->complete();
        }
        return $this->maybe_wp_die();
    }
    protected function memory_exceeded()
    {
        $memory_limit = $this->get_memory_limit() * 0.9;
        $current_memory = \memory_get_usage(\true);
        $return = \false;
        if ($current_memory >= $memory_limit) {
            $return = \true;
        }
        return \apply_filters($this->identifier . '_memory_exceeded', $return);
    }
    protected function get_memory_limit()
    {
        if (\function_exists('ini_get')) {
            $memory_limit = \ini_get('memory_limit');
        } else {
            $memory_limit = '128M';
        }
        if (!$memory_limit || -1 === \intval($memory_limit)) {
            $memory_limit = '32000M';
        }
        return \wp_convert_hr_to_bytes($memory_limit);
    }
    protected function time_exceeded()
    {
        $finish = $this->start_time + \apply_filters($this->identifier . '_default_time_limit', 20);
        $return = \false;
        if (\time() >= $finish) {
            $return = \true;
        }
        return \apply_filters($this->identifier . '_time_exceeded', $return);
    }
    protected function complete()
    {
        \delete_site_option($this->get_status_key());
        $this->clear_scheduled_event();
        $this->completed();
    }
    protected function completed()
    {
        \do_action($this->identifier . '_completed');
    }
    public function get_cron_interval()
    {
        $interval = 5;
        if (\property_exists($this, 'cron_interval')) {
            $interval = $this->cron_interval;
        }
        $interval = \apply_filters($this->cron_interval_identifier, $interval);
        return (\is_int($interval) && 0 < $interval) ? $interval : 5;
    }
    public function schedule_cron_healthcheck($schedules)
    {
        $interval = $this->get_cron_interval();
        if (1 === $interval) {
            $display = \__('Every Minute');
        } else {
            $display = \sprintf(\__('Every %d Minutes'), $interval);
        }
        $schedules[$this->cron_interval_identifier] = array('interval' => \MINUTE_IN_SECONDS * $interval, 'display' => $display);
        return $schedules;
    }
    public function handle_cron_healthcheck()
    {
        if ($this->is_processing()) {
            exit;
        }
        if ($this->is_queue_empty()) {
            $this->clear_scheduled_event();
            exit;
        }
        $this->dispatch();
    }
    protected function schedule_event()
    {
        if (!\wp_next_scheduled($this->cron_hook_identifier)) {
            \wp_schedule_event(\time() + $this->get_cron_interval() * \MINUTE_IN_SECONDS, $this->cron_interval_identifier, $this->cron_hook_identifier);
        }
    }
    protected function clear_scheduled_event()
    {
        $timestamp = \wp_next_scheduled($this->cron_hook_identifier);
        if ($timestamp) {
            \wp_unschedule_event($timestamp, $this->cron_hook_identifier);
        }
    }
    public function cancel_process()
    {
        $this->cancel();
    }
    abstract protected function task($item);
    protected static function maybe_unserialize($data, $allowed_classes)
    {
        if (\is_serialized($data)) {
            $options = array();
            if (\is_bool($allowed_classes) || \is_array($allowed_classes)) {
                $options['allowed_classes'] = $allowed_classes;
            }
            return @\unserialize($data, $options);
        }
        return $data;
    }
}
