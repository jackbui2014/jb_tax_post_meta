<?php
/*
Plugin Name: JB tax and post meta data
Plugin URI: http://jbprovider.com
Description:  JB tax and post meta data is quite possibly the best way  to <strong>create meta value for custom post type and custom taxonomy</strong>.
Version: 1.0
Author: JACK BUI
Author URI: http://jbprovider.com
License: GPLv2 or later
Text Domain: jb_theme
*/
require_once dirname(__FILE__) . '/class-jb-form.php';
require_once dirname(__FILE__) . '/class-jb-metabox.php';
/**
 * add metabox
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function add_meta_boxex() {

    $post_type   = 'post';
    $meta_box_id = $post_type . "_metabox";
    $title       = __('Shortcode', ET_DOMAIN);
    $arg         = array(
        'post_type' => $post_type,
        'context'   => 'advanced',
        'priority'  => 'default',
    );
    $input       = array(
        array(
            'title' => __( 'PDF file path', ET_DOMAIN ),
            'type'  => 'text',
            'name'  => 'pdf_path'
        ),

    );
    new JB_Metabox( $meta_box_id, $title, $arg, $input );
}
add_meta_boxex();