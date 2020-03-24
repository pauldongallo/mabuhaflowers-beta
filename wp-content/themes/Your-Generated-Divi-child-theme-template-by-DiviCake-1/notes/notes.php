<?php
/**
 * Register a custom post type called "Note".
 *
 * @see get_post_type_labels() for label keys.
 */
function note_cpt_init() {
    $labels = array(
        'name'                  => _x( 'Notes', 'Post type general name', 'note' ),
        'singular_name'         => _x( 'Note', 'Post type singular name', 'note' ),
        'menu_name'             => _x( 'Notes', 'Admin Menu text', 'note' ),
        'name_admin_bar'        => _x( 'Note', 'Add New on Toolbar', 'note' ),
        'add_new'               => __( 'Add New', 'note' ),
        'add_new_item'          => __( 'Add New Note', 'note' ),
        'new_item'              => __( 'New Note', 'note' ),
        'edit_item'             => __( 'Edit Note', 'note' ),
        'view_item'             => __( 'View Note', 'note' ),
        'all_items'             => __( 'All Notes', 'note' ),
        'search_items'          => __( 'Search Notes', 'note' ),
        'parent_item_colon'     => __( 'Parent Notes:', 'note' ),
        'not_found'             => __( 'No Notes found.', 'note' ),
        'not_found_in_trash'    => __( 'No Notes found in Trash.', 'note' ),
        'featured_image'        => _x( 'Note Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'note' ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'note' ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'note' ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'note' ),
        'archives'              => _x( 'Note archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'note' ),
        'insert_into_item'      => _x( 'Insert into Note', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'note' ),
        'uploaded_to_this_item' => _x( 'Uploaded to this Note', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'note' ),
        'filter_items_list'     => _x( 'Filter Notes list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'note' ),
        'items_list_navigation' => _x( 'Notes list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'note' ),
        'items_list'            => _x( 'Notes list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'note' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'notes' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'show_in_rest'       => true,
        'rest_base'          => 'notes',
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-exerpt-view',
        'supports'           => array( 'title', 'editor', 'author' ),
        'map_meta_cap'       => true,
    );

    register_post_type( 'note', $args );
}

add_action( 'init', 'note_cpt_init' );

/**
 * Flush rewrite rules on activation.
 */
function note_rewrite_flush() {
    note_cpt_init();
    note_rewrite_flush();
}