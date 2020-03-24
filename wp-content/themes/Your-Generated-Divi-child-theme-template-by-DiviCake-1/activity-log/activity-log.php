<?php
/**
 * Register a custom post type called "Activity Log".
 *
 * @see get_post_type_labels() for label keys.
 */
function activitylog_cpt_init() {
    $labels = array(
        'name'                  => _x( 'Activity Logs', 'Post type general name', 'activitylog' ),
        'singular_name'         => _x( 'Activity Log', 'Post type singular name', 'activitylog' ),
        'menu_name'             => _x( 'Activity Logs', 'Admin Menu text', 'activitylog' ),
        'name_admin_bar'        => _x( 'Activity Log', 'Add New on Toolbar', 'activitylog' ),
        'add_new'               => __( 'Add New', 'activitylog' ),
        'add_new_item'          => __( 'Add New Activity Log', 'activitylog' ),
        'new_item'              => __( 'New Activity Log', 'activitylog' ),
        'edit_item'             => __( 'Edit Activity Log', 'activitylog' ),
        'view_item'             => __( 'View Activity Log', 'activitylog' ),
        'all_items'             => __( 'All Activity Logs', 'activitylog' ),
        'search_items'          => __( 'Search Activity Logs', 'activitylog' ),
        'parent_item_colon'     => __( 'Parent Activity Logs:', 'activitylog' ),
        'not_found'             => __( 'No Activity Logs found.', 'activitylog' ),
        'not_found_in_trash'    => __( 'No Activity Logs found in Trash.', 'activitylog' ),
        'featured_image'        => _x( 'Activity Log Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'activitylog' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'activitylog' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'activitylog' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'activitylog' ),
        'archives'              => _x( 'Activity Log archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'activitylog' ),
        'insert_into_item'      => _x( 'Insert into Activity Log', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'activitylog' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this Activity Log', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'activitylog' ),
        'filter_items_list'     => _x( 'Filter Activity Logs list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'activitylog' ),
        'items_list_navigation' => _x( 'Activity Logs list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'activitylog' ),
        'items_list'            => _x( 'Activity Logs list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'activitylog' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'activitylogs' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'show_in_rest'       => true,
        'rest_base'          => 'activitylogs',
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-exerpt-view',
        'supports'           => array( 'title', 'author', 'custom-fields' ),
        'map_meta_cap'       => true,
    );

    register_post_type( 'activitylog', $args );
}

add_action( 'init', 'activitylog_cpt_init' );

/**
 * Flush rewrite rules on activation.
 */
function activitylog_rewrite_flush() {
    activitylog_cpt_init();
    activitylog_rewrite_flush();
}