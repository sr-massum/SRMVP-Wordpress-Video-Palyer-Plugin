<?php
/**
 * Plugin Name: SRM Video Player
 * Plugin URI: https://saieed.unaux.com/
 * Description: A modern, user-friendly video player plugin for WordPress, inspired by VLC Media Player UI.
 * Version: 1.0.0
 * Author: Saieed Rahman
 * Author URI: https://saieed.unaux.com/
 * License: GPL-2.0+
 * Text Domain: srm-video-player
 *
 * Copyright: SidMan Solution 2026
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SRMVP_VERSION', '1.0.0' );
define( 'SRMVP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SRMVP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SRMVP_PLUGIN_FILE', __FILE__ );

require_once SRMVP_PLUGIN_DIR . 'includes/class-srmvp-post-type.php';
require_once SRMVP_PLUGIN_DIR . 'includes/class-srmvp-shortcode.php';
require_once SRMVP_PLUGIN_DIR . 'includes/class-srmvp-settings.php';
require_once SRMVP_PLUGIN_DIR . 'admin/class-srmvp-admin.php';
require_once SRMVP_PLUGIN_DIR . 'public/class-srmvp-public.php';

if ( function_exists( 'register_block_type' ) ) {
    require_once SRMVP_PLUGIN_DIR . 'includes/class-srmvp-block.php';
}

register_activation_hook( __FILE__, 'srmvp_activate' );
register_deactivation_hook( __FILE__, 'srmvp_deactivate' );

function srmvp_activate() {
    $post_type = new SRMVP_Post_Type();
    $post_type->register();
    flush_rewrite_rules();

    $default_settings = array(
        'primary_color'   => '#ff6b35',
        'player_theme'    => 'dark',
        'show_controls'   => true,
        'autoplay'        => false,
        'loop'            => false,
        'mute_default'    => false,
        'custom_css'      => '',
    );
    if ( ! get_option( 'srmvp_settings' ) ) {
        update_option( 'srmvp_settings', $default_settings );
    }
}

function srmvp_deactivate() {
    flush_rewrite_rules();
}

function srmvp_init() {
    $post_type = new SRMVP_Post_Type();
    $post_type->register();

    $shortcode = new SRMVP_Shortcode();
    $shortcode->init();

    $admin = new SRMVP_Admin();
    $admin->init();

    $public = new SRMVP_Public();
    $public->init();

    if ( class_exists( 'SRMVP_Block' ) ) {
        $block = new SRMVP_Block();
        $block->init();
    }
}
add_action( 'init', 'srmvp_init' );
