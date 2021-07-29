<?php
/**
 * Plugin Name: 10up-primary-category
 * Plugin URI:  #
 * Description: Set primary category option
 * Version:     1.0
 * Author:      Mehedi Hasan
 * Author URI:  https://codewithmehedi.com
 * Text Domain: pcat
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

add_action( 'plugins_loaded', 'pcat_load_text_domain' );

/**
 * Load localization files
 *
 * @return void
 */
function pcat_load_text_domain() {

    load_plugin_textdomain( 'pcat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'pcat_register_custom_meta');

/**
 * Registers a meta key for posts.
 *
 * @since 4.9.8
 *
 * @param string $post_type Post type to register a meta key for. Pass an empty string
 *                          to register the meta key across all existing post types.
 * @param string $meta_key  The meta key to register.
 * @param array  $args      Data used to describe the meta key when registered. See
 *                          {@see register_meta()} for a list of supported arguments.
 * @return bool True if the meta key was successfully registered, false if not.
 */
function pcat_register_custom_meta() {
	register_post_meta( 'post', '_sp_cat', [
		'show_in_rest' => true,
		'single' => true,
        'type'  => 'string',
        'auth_callback' => function() {
            return current_user_can( 'edit_posts' );
        }
	] );
}

add_action( 'enqueue_block_editor_assets','pcat_register_scripts');
/**
* Fires after block assets have been enqueued for the editing interface.
*
* Call `add_action` on any hook before 'admin_enqueue_scripts'.
*
* In the function call you supply, simply use `wp_enqueue_script` and
* `wp_enqueue_style` to add your functionality to the block editor.
*
* @since 5.0.0
*/
function pcat_register_scripts() {

    /**
     * Enqueue a script.
     *
     * Registers the script if $src provided (does NOT overwrite), and enqueues it.
     *
     * @see WP_Dependencies::add()
     * @see WP_Dependencies::add_data()
     * @see WP_Dependencies::enqueue()
     *
     * @since 2.1.0
     *
     * @param string           $handle    Name of the script. Should be unique.
     * @param string           $src       Full URL of the script, or path of the script relative to the WordPress root directory.
     *                                    Default empty.
     * @param string[]         $deps      Optional. An array of registered script handles this script depends on. Default empty array.
     * @param string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
     *                                    as a query string for cache busting purposes. If version is set to false, a version
     *                                    number is automatically added equal to current installed WordPress version.
     *                                    If set to null, no version is added.
     * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
     *                                    Default 'false'.
     */
	wp_enqueue_script(
		'awp-custom-meta-plugin', 
		plugin_dir_url(__FILE__) . './build/index.js', 
		[ 'wp-edit-post', 'wp-element', 'wp-components', 'wp-plugins', 'wp-data' ],
		false,
		false
	);
}

/**
 * Filte defaut post loop if primary category is set.
 *
*/
$primaryCategory = get_post_meta( get_the_ID(), '_sp_cat', true );

if( $primaryCategory ) {

    function primary_category_query( $query ) {

        if ( ! is_admin() && $query->is_main_query() ) {
                $query->set( 'post_type', 'post' );
                $query->set( 'meta_key', '_sp_cat' );
                $query->set( 'order', 'DESC');
                $query->set( 'meta_query', array(
                    array(
                        'key'     => '_sp_cat',
                    )
                ) );
        }
    }
    add_action( 'pre_get_posts', 'primary_category_query' );
}
