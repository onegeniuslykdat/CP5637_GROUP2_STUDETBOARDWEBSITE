<?php

namespace Staatic\Vendor;

abstract class WP_Async_Request
{
    protected $prefix = 'wp';
    protected $action = 'async_request';
    protected $identifier;
    protected $data = array();
    public function __construct()
    {
        $this->identifier = $this->prefix . '_' . $this->action;
        \add_action('wp_ajax_' . $this->identifier, array($this, 'maybe_handle'));
        \add_action('wp_ajax_nopriv_' . $this->identifier, array($this, 'maybe_handle'));
    }
    public function data($data)
    {
        $this->data = $data;
        return $this;
    }
    public function dispatch()
    {
        $url = \add_query_arg($this->get_query_args(), $this->get_query_url());
        $args = $this->get_post_args();
        return \wp_remote_post(\esc_url_raw($url), $args);
    }
    protected function get_query_args()
    {
        if (\property_exists($this, 'query_args')) {
            return $this->query_args;
        }
        $args = array('action' => $this->identifier, 'nonce' => \wp_create_nonce($this->identifier));
        return \apply_filters($this->identifier . '_query_args', $args);
    }
    protected function get_query_url()
    {
        if (\property_exists($this, 'query_url')) {
            return $this->query_url;
        }
        $url = \admin_url('admin-ajax.php');
        return \apply_filters($this->identifier . '_query_url', $url);
    }
    protected function get_post_args()
    {
        if (\property_exists($this, 'post_args')) {
            return $this->post_args;
        }
        $args = array('timeout' => 5, 'blocking' => \false, 'body' => $this->data, 'cookies' => $_COOKIE, 'sslverify' => \apply_filters('https_local_ssl_verify', \false));
        return \apply_filters($this->identifier . '_post_args', $args);
    }
    public function maybe_handle()
    {
        \session_write_close();
        \check_ajax_referer($this->identifier, 'nonce');
        $this->handle();
        return $this->maybe_wp_die();
    }
    protected function maybe_wp_die($return = null)
    {
        if (\apply_filters($this->identifier . '_wp_die', \true)) {
            \wp_die();
        }
        return $return;
    }
    abstract protected function handle();
}
