<?php

/**
 * This file contains scripts and styles that will be enqueued to the website.
 *
 * @package Variations
 *
 */

if (!function_exists('variations_frontend_assets')) {
    /**
     * Enqueue scripts and styles on the website frontend.
     * 
     * @return void
     */
    function variations_frontend_assets()
    {

        /**
         *  Frontend Styles.
         * */
        wp_enqueue_style(
            'variations-frontend-style',
            get_template_directory_uri() . '/assets/css/frontend.css',
            array(),
            VARIATIONS_THEME_VERSION
        );

        /**
         *  Woocommerce Styles.
         * */
        wp_enqueue_style(
            'variations-woocommerce-style',
            get_template_directory_uri() . '/assets/woocommerce/index.css',
            array('variations-frontend-style', 'woocommerce-blocktheme', 'woocommerce-general'),
            VARIATIONS_THEME_VERSION
        );

        /**
         *  Frontend JavaScript.
         * */
        wp_enqueue_script(
            'variations-frontend-script',
            get_template_directory_uri() . '/assets/js/frontend.js',
            array('jquery'),
            VARIATIONS_THEME_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'variations_frontend_assets');

if (!function_exists('variations_editor_assets')) {
    /**
     * Enqueue scripts and styles for the website editor.
     * 
     * @return void
     */
    function variations_editor_assets()
    {        
        /**
         * Check If it is admin part.
         * */
        if ( is_admin() ) {
            /**
             * Editor Styles.
             * */
            wp_enqueue_style(
                'variations-editor-style',
                get_template_directory_uri() . '/assets/css/editor.css',
                array(),
                VARIATIONS_THEME_VERSION
            );

            /**
             * Editor Scripts.
             * */
            wp_enqueue_script(
                'variations-editor-script',
                get_template_directory_uri() . '/assets/js/editor.js',
                array('wp-blocks'),
                VARIATIONS_THEME_VERSION
            );

            /**
             *  If WooCommerce is activated.
             * */
            if ( class_exists( 'WooCommerce' ) ) {

                /**
                 *  WooCommerce Styles.
                 * */
                wp_enqueue_style(
                    'variations-woocommerce-style',
                    get_template_directory_uri() . '/assets/woocommerce/index.css',
                    array('woocommerce-blocktheme', 'woocommerce-smallscreen', 'woocommerce-general'),
                    VARIATIONS_THEME_VERSION
                );

                /**
                 * WooCommerce Editor Scripts.
                 * */
                wp_enqueue_script(
                    'variations-woocommerce-editor-script',
                    get_template_directory_uri() . '/assets/woocommerce/index.js',
                    array('wp-blocks'),
                    VARIATIONS_THEME_VERSION
                );
            }
        }
    }
}
add_action('enqueue_block_assets', 'variations_editor_assets');

if (!function_exists('variations_editor_frontend_assets')) {
    /**
     * Enqueue scripts and styles for the website editor and frontend.
     * 
     * @return void
     */
    function variations_editor_frontend_assets()
    {
        /**
         * Editor/Frontend Styles.
         * */
        wp_enqueue_style(
            'variations-editor-frontend-style',
            get_template_directory_uri() . '/assets/css/editor-frontend.css',
            array(),
            VARIATIONS_THEME_VERSION
        );
    }
}
add_action('enqueue_block_assets', 'variations_editor_frontend_assets');
