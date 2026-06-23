<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SRMVP_Public {

    public function init() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_head', array( $this, 'output_custom_css' ) );
    }

    public function enqueue_assets() {
        wp_enqueue_style(
            'srmvp-player',
            SRMVP_PLUGIN_URL . 'assets/css/player.css',
            array(),
            SRMVP_VERSION
        );

        wp_enqueue_script(
            'srmvp-player',
            SRMVP_PLUGIN_URL . 'assets/js/player.js',
            array(),
            SRMVP_VERSION,
            true
        );

        $settings = SRMVP_Settings::get();
        wp_add_inline_style( 'srmvp-player', $this->generate_css_vars( $settings ) );
    }

    private function generate_css_vars( $settings ) {
        $accent = esc_attr( $settings['primary_color'] );
        return ":root { --srmvp-accent: {$accent}; }";
    }

    public function output_custom_css() {
        $custom_css = SRMVP_Settings::get( 'custom_css' );
        if ( ! empty( $custom_css ) ) {
            echo '<style id="srmvp-custom-css">' . wp_strip_all_tags( $custom_css ) . '</style>' . "\n";
        }
    }
}
