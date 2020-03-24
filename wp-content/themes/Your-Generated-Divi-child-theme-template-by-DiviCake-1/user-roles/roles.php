<?php

$result = add_role(
    'mod_agent',
    __( 'Agent' ),
    array(
        'delete_posts'       	   => true,  // true allows this capability
        'delete_published_posts'   => true,
        'edit_posts' 			   => true, // Use false to explicitly deny
        'edit_published_posts' 		=> true, // Use false to explicitly deny
        'publish_posts' 			=> true, // Use false to explicitly deny
        'read' 			   			=> true, // Use false to explicitly deny
        'upload_files' 			    => true, // Use false to explicitly deny
    )
);

/**
 * Register Agent role.
 */
// function mabuhay_register_role() {
// 	add_role( 'mod_agent', 'Agent' );
// }

/**
 * Remove Agent role.
 */
// function mabuhay_remove_role() {
// 	remove_role( 'mod_agent', 'Agent' );
// }

/**
 * Grant Task-level capabilities to Administrator, Editor, and Task Logger.
 */
// function mabuhay_add_capabilities() {

// 	$roles = array( 'mod_agent', );

// 	foreach( $roles as $the_role ) {
// 		$role = get_role( $the_role );
// 		$role->add_cap( 'delete_posts' );
// 		$role->add_cap( 'delete_published_posts' );
// 		$role->add_cap( 'edit_posts' );
// 		$role->add_cap( 'edit_published_posts' );
// 		$role->add_cap( 'publish_posts' );
// 		$role->add_cap( 'read' );
// 		$role->add_cap( 'upload_files' );
// 	}

// }