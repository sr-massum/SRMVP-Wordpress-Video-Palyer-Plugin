<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap srmvp-admin-wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-video-alt3"></span>
        <?php esc_html_e( 'All Videos', 'srm-video-player' ); ?>
    </h1>
    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=srm_video' ) ); ?>" class="page-title-action">
        <?php esc_html_e( 'Add New', 'srm-video-player' ); ?>
    </a>

    <form method="get">
        <input type="hidden" name="page" value="srmvp">
        <p class="search-box">
            <input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search videos...', 'srm-video-player' ); ?>">
            <button type="submit" class="button"><?php esc_html_e( 'Search', 'srm-video-player' ); ?></button>
        </p>
    </form>

    <?php if ( empty( $videos ) ) : ?>
        <div class="srmvp-empty-state">
            <span class="dashicons dashicons-video-alt3 srmvp-empty-icon"></span>
            <p><?php esc_html_e( 'No videos found.', 'srm-video-player' ); ?></p>
            <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=srm_video' ) ); ?>" class="button button-primary">
                <?php esc_html_e( 'Add Your First Video', 'srm-video-player' ); ?>
            </a>
        </div>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped srmvp-videos-table">
            <thead>
                <tr>
                    <th class="column-thumb"><?php esc_html_e( 'Thumbnail', 'srm-video-player' ); ?></th>
                    <th class="column-title"><?php esc_html_e( 'Title', 'srm-video-player' ); ?></th>
                    <th class="column-source"><?php esc_html_e( 'Source', 'srm-video-player' ); ?></th>
                    <th class="column-shortcode"><?php esc_html_e( 'Shortcode', 'srm-video-player' ); ?></th>
                    <th class="column-status"><?php esc_html_e( 'Status', 'srm-video-player' ); ?></th>
                    <th class="column-date"><?php esc_html_e( 'Date', 'srm-video-player' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $videos as $video ) :
                    $source_type = get_post_meta( $video->ID, '_srmvp_source_type', true ) ?: 'upload';
                    $thumbnail   = get_the_post_thumbnail_url( $video->ID, 'thumbnail' );
                    if ( ! $thumbnail ) {
                        $custom_thumb = get_post_meta( $video->ID, '_srmvp_thumbnail', true );
                        $thumbnail    = $custom_thumb ? wp_get_attachment_image_url( $custom_thumb, 'thumbnail' ) : '';
                    }
                    $source_labels = array(
                        'upload'   => __( 'Local Upload', 'srm-video-player' ),
                        'youtube'  => __( 'YouTube', 'srm-video-player' ),
                        'facebook' => __( 'Facebook', 'srm-video-player' ),
                        'gdrive'   => __( 'Google Drive', 'srm-video-player' ),
                    );
                ?>
                <tr>
                    <td class="column-thumb">
                        <?php if ( $thumbnail ) : ?>
                            <img src="<?php echo esc_url( $thumbnail ); ?>" width="80" height="50" alt="" style="object-fit:cover;border-radius:4px;">
                        <?php else : ?>
                            <div class="srmvp-no-thumb">
                                <span class="dashicons dashicons-format-video"></span>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="column-title">
                        <strong>
                            <a href="<?php echo esc_url( get_edit_post_link( $video->ID ) ); ?>">
                                <?php echo esc_html( $video->post_title ); ?>
                            </a>
                        </strong>
                        <div class="row-actions">
                            <span><a href="<?php echo esc_url( get_edit_post_link( $video->ID ) ); ?>"><?php esc_html_e( 'Edit', 'srm-video-player' ); ?></a> | </span>
                            <span class="trash"><a href="<?php echo esc_url( get_delete_post_link( $video->ID ) ); ?>" class="submitdelete"><?php esc_html_e( 'Trash', 'srm-video-player' ); ?></a></span>
                        </div>
                    </td>
                    <td class="column-source">
                        <span class="srmvp-source-badge srmvp-source-<?php echo esc_attr( $source_type ); ?>">
                            <?php echo esc_html( $source_labels[ $source_type ] ?? $source_type ); ?>
                        </span>
                    </td>
                    <td class="column-shortcode">
                        <code>[srmvp id="<?php echo esc_attr( $video->ID ); ?>"]</code>
                    </td>
                    <td class="column-status">
                        <?php echo esc_html( ucfirst( $video->post_status ) ); ?>
                    </td>
                    <td class="column-date">
                        <?php echo esc_html( get_the_date( 'Y/m/d', $video->ID ) ); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ( $total > 20 ) :
            $pages = ceil( $total / 20 );
            echo '<div class="tablenav bottom"><div class="tablenav-pages">';
            for ( $i = 1; $i <= $pages; $i++ ) :
                $url = add_query_arg( array( 'page' => 'srmvp', 'paged' => $i ), admin_url( 'admin.php' ) );
                $class = ( $i === $paged ) ? 'current' : '';
                echo '<a href="' . esc_url( $url ) . '" class="page-numbers ' . esc_attr( $class ) . '">' . esc_html( $i ) . '</a>';
            endfor;
            echo '</div></div>';
        endif; ?>
    <?php endif; ?>
</div>
