<?php
/**
 * Sidebar template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Determine the correct sidebar to use. If we're on a category page, we'll use the sidebar for that category.
$sidebar_id = 'sidebar-1';
if ( is_product_category() ) {
	$term = get_queried_object();
	$sidebar_id = 'sidebar-' . $term->slug;
}

if ( is_active_sidebar( $sidebar_id ) ) : ?>
	<div id="sidebar">
		<?php dynamic_sidebar( $sidebar_id ); ?>
	</div>
<?php
endif;
