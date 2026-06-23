<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SRMVP_Admin {

    public function init() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_srm_video', array( $this, 'save_meta' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
        add_action( 'admin_post_srmvp_save_settings', array( $this, 'handle_save_settings' ) );
    }

    public function enqueue_block_editor_assets() {
        wp_enqueue_style(
            'srmvp-block-editor-styles',
            SRMVP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SRMVP_VERSION
        );
    }

    public function enqueue_scripts( $hook ) {
        $screens = array( 'post.php', 'post-new.php', 'srmvp_page_srmvp-settings', 'toplevel_page_srmvp' );
        if ( ! in_array( $hook, $screens ) && strpos( $hook, 'srmvp' ) === false ) {
            return;
        }

        wp_enqueue_media();

        wp_enqueue_style(
            'wp-color-picker'
        );

        wp_enqueue_style(
            'srmvp-admin',
            SRMVP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SRMVP_VERSION
        );

        wp_enqueue_script(
            'srmvp-admin',
            SRMVP_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery', 'wp-color-picker', 'media-upload', 'thickbox' ),
            SRMVP_VERSION,
            true
        );

        wp_localize_script( 'srmvp-admin', 'srmvpAdmin', array(
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'mediaTitle'   => __( 'Select Video', 'srm-video-player' ),
            'mediaButton'  => __( 'Use this video', 'srm-video-player' ),
            'thumbTitle'   => __( 'Select Thumbnail', 'srm-video-player' ),
            'thumbButton'  => __( 'Use this image', 'srm-video-player' ),
        ) );
    }

    public function add_menu() {
        add_menu_page(
            __( 'SRM Video Player', 'srm-video-player' ),
            __( 'SRMVP', 'srm-video-player' ),
            'manage_options',
            'srmvp',
            array( $this, 'render_all_videos' ),
            'dashicons-video-alt3',
            25
        );

        add_submenu_page(
            'srmvp',
            __( 'All Videos', 'srm-video-player' ),
            __( 'All Videos', 'srm-video-player' ),
            'manage_options',
            'srmvp',
            array( $this, 'render_all_videos' )
        );

        add_submenu_page(
            'srmvp',
            __( 'Add New Video', 'srm-video-player' ),
            __( 'Add New Video', 'srm-video-player' ),
            'manage_options',
            'post-new.php?post_type=srm_video'
        );

        add_submenu_page(
            'srmvp',
            __( 'Settings', 'srm-video-player' ),
            __( 'Settings', 'srm-video-player' ),
            'manage_options',
            'srmvp-settings',
            array( $this, 'render_settings' )
        );
    }

    public function render_all_videos() {
        $paged  = max( 1, absint( $_GET['paged'] ?? 1 ) );
        $search = sanitize_text_field( $_GET['s'] ?? '' );

        $query_args = array(
            'post_type'      => 'srm_video',
            'posts_per_page' => 20,
            'paged'          => $paged,
            'post_status'    => array( 'publish', 'draft' ),
        );

        if ( $search ) {
            $query_args['s'] = $search;
        }

        $query  = new WP_Query( $query_args );
        $videos = $query->posts;
        $total  = $query->found_posts;
        include SRMVP_PLUGIN_DIR . 'admin/views/all-videos.php';
        wp_reset_postdata();
    }

    public function render_settings() {
        $settings = SRMVP_Settings::get();
        include SRMVP_PLUGIN_DIR . 'admin/views/settings.php';
    }

    public function add_meta_boxes() {
        add_meta_box(
            'srmvp_video_details',
            __( 'Video Details', 'srm-video-player' ),
            array( $this, 'render_meta_box' ),
            'srm_video',
            'normal',
            'high'
        );

        add_meta_box(
            'srmvp_shortcode_info',
            __( 'Shortcode', 'srm-video-player' ),
            array( $this, 'render_shortcode_box' ),
            'srm_video',
            'side',
            'default'
        );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'srmvp_save_meta', 'srmvp_meta_nonce' );

        $source_type = get_post_meta( $post->ID, '_srmvp_source_type', true ) ?: 'upload';
        $video_url   = get_post_meta( $post->ID, '_srmvp_video_url', true );
        $title_color = get_post_meta( $post->ID, '_srmvp_title_color', true ) ?: '#ffffff';
        $thumbnail   = get_post_meta( $post->ID, '_srmvp_thumbnail', true );
        $thumb_url   = $thumbnail ? wp_get_attachment_image_url( $thumbnail, 'medium' ) : '';

        include SRMVP_PLUGIN_DIR . 'admin/views/meta-box.php';
    }

    public function render_shortcode_box( $post ) {
        ?>
        <div class="srmvp-shortcode-info">
            <p><?php esc_html_e( 'Use this shortcode to embed the video:', 'srm-video-player' ); ?></p>
            <code class="srmvp-shortcode-code">[srmvp id="<?php echo esc_attr( $post->ID ); ?>"]</code>
            <button type="button" class="button button-small srmvp-copy-shortcode" data-shortcode='[srmvp id="<?php echo esc_attr( $post->ID ); ?>"]'>
                <?php esc_html_e( 'Copy', 'srm-video-player' ); ?>
            </button>
        </div>
        <?php
    }

    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['srmvp_meta_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['srmvp_meta_nonce'] ) ), 'srmvp_save_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $allowed_sources = array( 'upload', 'youtube', 'facebook', 'gdrive' );
        $source_type     = sanitize_text_field( $_POST['srmvp_source_type'] ?? 'upload' );
        if ( ! in_array( $source_type, $allowed_sources ) ) {
            $source_type = 'upload';
        }

        $video_url   = esc_url_raw( $_POST['srmvp_video_url'] ?? '' );
        $title_color = sanitize_hex_color( $_POST['srmvp_title_color'] ?? '#ffffff' );
        $thumbnail   = absint( $_POST['srmvp_thumbnail'] ?? 0 );

        update_post_meta( $post_id, '_srmvp_source_type', $source_type );
        update_post_meta( $post_id, '_srmvp_video_url', $video_url );
        update_post_meta( $post_id, '_srmvp_title_color', $title_color );
        update_post_meta( $post_id, '_srmvp_thumbnail', $thumbnail );
    }

    public function handle_save_settings() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized.', 'srm-video-player' ) );
        }
        check_admin_referer( 'srmvp_settings_nonce', 'srmvp_settings_nonce' );

        SRMVP_Settings::save( $_POST );

        wp_safe_redirect( add_query_arg( array(
            'page'    => 'srmvp-settings',
            'updated' => '1',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }
}
