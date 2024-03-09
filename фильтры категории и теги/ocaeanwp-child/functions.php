<?php


function oceanwp_child_enqueue_parent_style() {
	 // Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
	$theme   = wp_get_theme( 'OceanWP' );
	$version = $theme->get( 'Version' );
	// Load the stylesheet
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'oceanwp-style' ), $version );
	wp_enqueue_style( 'ow-flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css' );
	wp_enqueue_script( 'ow-flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr' );

	wp_enqueue_style( 'ow-nouislider-css', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.4.0/nouislider.css' );

	wp_enqueue_script( 'ow-nouislider', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.4.0/nouislider.min.js' );

	wp_enqueue_script( 'add-ons-js', get_stylesheet_directory_uri() . '/assets/js/add-ons.js', array( 'jquery' ), time(), true );
}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style' );

require_once get_stylesheet_directory() . '/add-ons/post-types.php';
require_once get_stylesheet_directory() . '/add-ons/filters/base-filter/base-filter.php';
require_once get_stylesheet_directory() . '/add-ons/filters/filter-functions.php';
// require_once get_stylesheet_directory() . '/add-ons/gutenberg/gutenberg.php';

// add_filter('query_vars', function ($public_query_vars) {
// var_dump($public_query_vars);
// return $public_query_vars;
// });
function debug($data){
	echo '<pre>'. print_r($data, true) .'</pre>';
}

 
function add_filter_archive_event() {
	global $wp;
	echo get_filter_by_taxonomy_links( 'events_category', 'По категории', '');
	// echo get_filter_by_taxonomy_forms('events_category', false, 'По категории', 'AND');
	echo get_filter_by_taxonomy_links( 'events_tags', 'По тегам', '' );
	echo get_filter_by_taxonomy_links( 'events_brends', 'По брендам', '' );
}
add_action( 'events_add_filter_sidebar', 'add_filter_archive_event' );
