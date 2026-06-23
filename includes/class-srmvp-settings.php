<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SRMVP_Settings {

    public static function get( $key = null ) {
        $defaults = array(
            'primary_color' => '#ff6b35',
            'player_theme'  => 'dark',
            'show_controls' => true,
            'autoplay'      => false,
            'loop'          => false,
            'mute_default'  => false,
            'custom_css'    => '',
        );

        $settings = get_option( 'srmvp_settings', $defaults );
        $settings = wp_parse_args( $settings, $defaults );

        if ( $key ) {
            return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
        }

        return $settings;
    }

    public static function save( $data ) {
        $settings = array(
            'primary_color' => sanitize_hex_color( $data['primary_color'] ?? '#ff6b35' ),
            'player_theme'  => in_array( $data['player_theme'] ?? '', array( 'dark', 'light' ) ) ? $data['player_theme'] : 'dark',
            'show_controls' => ! empty( $data['show_controls'] ),
            'autoplay'      => ! empty( $data['autoplay'] ),
            'loop'          => ! empty( $data['loop'] ),
            'mute_default'  => ! empty( $data['mute_default'] ),
            'custom_css'    => wp_strip_all_tags( $data['custom_css'] ?? '' ),
        );

        update_option( 'srmvp_settings', $settings );
        return $settings;
    }
}
