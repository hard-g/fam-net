<?php

function famnet_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'famnet_enqueue_styles' );

/**
 * When visiting the product directory, ensure that a category is selected.
 */
function famnet_redirect_to_category() {
	// If this is not the WooCommerce shop page, bail.
	if ( ! is_shop() ) {
		return;
	}

	if ( is_product_category() ) {
		return;
	}

	// Get the first category.
	$categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	) );

	// Exclude uncategorized.
	$categories = array_filter( $categories, function( $category ) {
		return 'uncategorized' !== $category->slug;
	} );

	// If there are no categories, bail.
	if ( empty( $categories ) ) {
		return;
	}

	// Get the shop URL.
	$shop_url = get_permalink( wc_get_page_id( 'shop' ) );

	// Get the first category's URL.
	$category = array_shift( $categories );
	$url      = add_query_arg( 'product_cat', $category->slug, $shop_url );

	// Redirect to the category.
	wp_safe_redirect( $url );
}
add_action( 'template_redirect', 'famnet_redirect_to_category' );

/**
 * Unhook WC's default archive description and hook our own.
 */
function famnet_archive_description() {
	$description = '';

	// Visit Dashboard > Products > Categories and add a description to the category.
	if ( is_product_category() ) {
		$term        = get_queried_object();
		$description = $term->description;
	}

	echo '<div class="term-description">' . wpautop( do_shortcode( $description ) ) . '</div>';
}
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
add_action( 'woocommerce_archive_description', 'famnet_archive_description', 10 );
add_action( 'woocommerce_archive_description', 'famnet_archive_description', 10 );

/**
 * Register one sidebar for each WooCommerce product category.
 *
 * This allows the display of different widgets on different product category pages.
 */
function famnet_register_sidebars() {
	$categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	) );

	if ( is_wp_error( $categories ) ) {
		return;
	}

	// Exclude uncategorized.
	$categories = array_filter( $categories, function( $category ) {
		return 'uncategorized' !== $category->slug;
	} );

	// If there are no categories, bail.
	if ( empty( $categories ) ) {
		return;
	}

	foreach ( $categories as $category ) {
		register_sidebar( array(
			'name'          => sprintf( 'Sidebar for %s', $category->name ),
			'id'            => 'sidebar-' . $category->slug,
			'description'   => 'Widgets in this area will be shown on the ' . $category->name . ' category page.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
		) );
	}
}
add_action( 'init', 'famnet_register_sidebars', 200 );
