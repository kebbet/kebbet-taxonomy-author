<?php
/**
 * Plugin Name:       Kebbet plugins - custom taxonomy: author
 * Plugin URI:        https://github.com/kebbet/kebbet-taxonomy-author
 * Description:       Register the custom taxonomy author
 * Version:           20210627.01
 * Author:            Erik Betshammar
 * Author URI:        https://verkan.se
 * Requires at least: 5.7
 * Requires PHP:      7.4
 *
 * @package kebbet-taxonomy-author
 * @author Erik Betshammar
 */

namespace kebbet\taxonomy\author;

const TAXONOMY   = 'author-tag';
const POST_TYPES = array( 'explore' );
const HIDE_SLUG  = false;

/**
 * Hook into the 'init' action
 */
function init() {
	load_textdomain();
	register();
}
add_action( 'init', __NAMESPACE__ . '\init', 0 );

/**
 * Flush rewrite rules on registration.
 */
function rewrite_flush() {
	// First, we "add" the custom taxonomy via the above written function.
	register();

	// ATTENTION: This is *only* done during plugin activation hook in this example!
	// You should *NEVER EVER* do this on every page load!!
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\rewrite_flush' );

/**
 * Load plugin textdomain.
 */
function load_textdomain() {
	load_plugin_textdomain( 'kebbet-taxonomy-author', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register the taxonomy
 */
function register() {

	$tax_labels = array(
		'name'                       => _x( 'Authors', 'taxonomy general name', 'kebbet-taxonomy-author' ),
		'menu_name'                  => __( 'Authors', 'kebbet-taxonomy-author' ),
		'singular_name'              => _x( 'Author', 'taxonomy singular name', 'kebbet-taxonomy-author' ),
		'all_items'                  => __( 'All author tags', 'kebbet-taxonomy-author' ),
		'edit_item'                  => __( 'Edit tag', 'kebbet-taxonomy-author' ),
		'view_item'                  => __( 'View tag', 'kebbet-taxonomy-author' ),
		'update_item'                => __( 'Update tag', 'kebbet-taxonomy-author' ),
		'add_new_item'               => __( 'Add new tag', 'kebbet-taxonomy-author' ),
		'new_item_name'              => __( 'New tag name', 'kebbet-taxonomy-author' ),
		'separate_items_with_commas' => __( 'Separate author tags with commas', 'kebbet-taxonomy-author' ),
		'search_items'               => __( 'Search tags', 'kebbet-taxonomy-author' ),
		'add_or_remove_items'        => __( 'Add or remove tags', 'kebbet-taxonomy-author' ),
		'choose_from_most_used'      => __( 'Choose from the most used author tags', 'kebbet-taxonomy-author' ),
		'not_found'                  => __( 'No tags found.', 'kebbet-taxonomy-author' ),
		'popular_items'              => __( 'Popular tags', 'kebbet-taxonomy-author' ),
		'parent_item'                => __( 'Parent tag', 'kebbet-taxonomy-author' ),
		'parent_item_colon'          => __( 'Parent tag:', 'kebbet-taxonomy-author' ),
		'back_to_items'              => __( '&larr; Back to tags', 'kebbet-taxonomy-author' ),
	);

	$capabilities = array(
		'manage_terms' => 'manage_categories', // Previous 'manage_options'.
		'edit_terms'   => 'manage_categories', // Previous 'manage_options'.
		'delete_terms' => 'manage_categories', // Previous 'manage_options'.
		'assign_terms' => 'publish_posts',
	);

	$tax_args = array(
		'capabilities'          => $capabilities,
		'hierarchical'          => false,
		'has_archive'           => false,
		'labels'                => $tax_labels,
		'public'                => false,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'show_in_nav_menus'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => false,
		'show_in_rest'          => true,
		'rewrite'               => array(),
		'description'           => __( 'Author tag.', 'kebbet-taxonomy-author' ),
	);

	register_taxonomy( TAXONOMY, POST_TYPES, $tax_args );
}

/**
 * Remove the 'slug' column from the table in 'edit-tags.php'
 */
function remove_column_slug( $columns ) {
    if ( isset( $columns['slug'] ) )
        unset( $columns['slug'] );   

    return $columns;
}

/**
 * Run filter only if constant says so.
 */
if ( true === HIDE_SLUG ) {
	add_filter( 'manage_edit-' . TAXONOMY . '_columns', __NAMESPACE__ . '\remove_column_slug' );
}
