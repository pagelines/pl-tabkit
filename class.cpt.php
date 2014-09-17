<?php

class PL_TabKit_CPT {

    function __construct() {
        // Hook into the 'init' action
        add_action( 'init', array( $this, 'setup_posttype' ), 0 );
}



    function setup_posttype() {

	$labels = array(
		'name'                => _x( 'TabKits', 'Post Type General Name', 'pagelines' ),
		'singular_name'       => _x( 'TabKit', 'Post Type Singular Name', 'pagelines' ),
		'menu_name'           => __( 'TabKit', 'pagelines' ),
		'parent_item_colon'   => __( 'Parent Item:', 'pagelines' ),
		'all_items'           => __( 'All Items', 'pagelines' ),
		'view_item'           => __( 'View Item', 'pagelines' ),
		'add_new_item'        => __( 'Add New Item', 'pagelines' ),
		'add_new'             => __( 'Add New', 'pagelines' ),
		'edit_item'           => __( 'Edit Item', 'pagelines' ),
		'update_item'         => __( 'Update Item', 'pagelines' ),
		'search_items'        => __( 'Search Item', 'pagelines' ),
		'not_found'           => __( 'Not found', 'pagelines' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'pagelines' ),
	);
	$args = array(
		'label'               => __( 'tabkit', 'pagelines' ),
		'description'         => __( 'Custom Post Type for TabKit Section', 'pagelines' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'thumbnail', ),
		'taxonomies'          => array( 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 75,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'tabkit', $args );


    register_taxonomy( 'tabkit_category', 'tabkit',
       array(
          'hierarchical' => true,
          'has_archive'         => true,
          'can_export'          => true,
          'label' => 'Categories',
          'query_var' => TRUE,
          'rewrite' => array(
              'slug' => 'tabkit_category',
              'with_front' => true,
              'hierarchical' => true,
          ),
       )
    );
}

}

new PL_TabKit_CPT;

// utility
//
function tabkit_get_categories( $tax = 'tabkit_category' ) {

    $taxs = array();
    $tax_terms = get_terms($tax);

    foreach ($tax_terms as $tax_term) {
        $taxs[$tax_term->term_id] = array(

            'link'  => esc_attr(add_query_arg( array( 'post_type' => 'tabkit' ), get_term_link($tax_term, $tax)) ),
            'name'  => $tax_term->name
        );

    }
    return $taxs;
}
