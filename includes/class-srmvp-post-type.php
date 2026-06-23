<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SRMVP_Post_Type {

    public function register() {
        $labels = array(
            'name'               => __( 'Videos', 'srm-video-player' ),
            'singular_name'      => __( 'Video', 'srm-video-player' ),
            'menu_name'          => __( 'Videos', 'srm-video-player' ),
            'add_new'            => __( 'Add New Video', 'srm-video-player' ),
            'add_new_item'       => __( 'Add New Video', 'srm-video-player' ),
            'edit_item'          => __( 'Edit Video', 'srm-video-player' ),
            'new_item'           => __( 'New Video', 'srm-video-player' ),
            'view_item'          => __( 'View Video', 'srm-video-player' ),
            'search_items'       => __( 'Search Videos', 'srm-video-player' ),
            'not_found'          => __( 'No videos found', 'srm-video-player' ),
            'not_found_in_trash' => __( 'No videos found in Trash', 'srm-video-player' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_in_rest'       => true,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'supports'           => array( 'title', 'thumbnail' ),
            'rewrite'            => false,
        );

        register_post_type( 'srm_video', $args );

        $this->register_meta();
    }

    private function register_meta() {
        register_post_meta( 'srm_video', '_srmvp_source_type', array(
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'default'           => 'upload',
            'sanitize_callback' => function( $value ) {
                $allowed = array( 'upload', 'youtube', 'facebook', 'gdrive' );
                return in_array( $value, $allowed, true ) ? $value : 'upload';
            },
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );

        register_post_meta( 'srm_video', '_srmvp_video_url', array(
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );

        register_post_meta( 'srm_video', '_srmvp_title_color', array(
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'string',
            'default'           => '#ffffff',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );

        register_post_meta( 'srm_video', '_srmvp_thumbnail', array(
            'show_in_rest'      => true,
            'single'            => true,
            'type'              => 'integer',
            'default'           => 0,
            'sanitize_callback' => 'absint',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        ) );
    }
}
