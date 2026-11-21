<?php
/**
 * GeneratePress Child Theme functions and definitions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display breadcrumbs above single post titles.
 */
add_action( 'generate_before_entry_title', 'gp_child_add_breadcrumbs' );

function gp_child_add_breadcrumbs() {

	if ( ! is_single() ) {
		return;
	}

	$post_id = get_the_ID();

	if ( ! $post_id ) {
		return;
	}

	echo '<nav class="custom-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'generatepress-child' ) . '">';

	echo '<a href="' . esc_url( home_url( '/' ) ) . '">';
	echo esc_html__( 'Home', 'generatepress-child' );
	echo '</a> &gt; ';

	$categories = get_the_category( $post_id );

	if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {

		$category = $categories[0];

		// Retrieve parent categories in hierarchical order.
		$parents = get_ancestors( $category->term_id, 'category', 'taxonomy' );

		if ( ! empty( $parents ) ) {

			$parents = array_reverse( $parents );

			foreach ( $parents as $parent_id ) {

				$parent = get_term( $parent_id, 'category' );

				if ( is_wp_error( $parent ) || ! $parent ) {
					continue;
				}

				$parent_link = get_term_link( $parent );

				if ( is_wp_error( $parent_link ) ) {
					continue;
				}

				echo '<a href="' . esc_url( $parent_link ) . '">';
				echo esc_html( $parent->name );
				echo '</a> &gt; ';
			}
		}

		// Display the current category.
		$category_link = get_term_link( $category );

		if ( ! is_wp_error( $category_link ) ) {

			echo '<a href="' . esc_url( $category_link ) . '">';
			echo esc_html( $category->name );
			echo '</a> &gt; ';
		}
	}

	// Display the current post title.
	echo '<span>' . esc_html( get_the_title( $post_id ) ) . '</span>';

	echo '</nav>';
}


/**
 * Display social sharing buttons after single post content.
 */
add_action( 'generate_after_entry_content', 'gp_child_add_share_buttons' );

function gp_child_add_share_buttons() {

	if ( ! is_single() ) {
		return;
	}

	$permalink = get_permalink();
	$title     = get_the_title();

	if ( ! $permalink ) {
		return;
	}

	$url = rawurlencode( $permalink );

	$encoded_title = rawurlencode( $title );

	$x_url = 'https://x.com/intent/tweet?url=' . $url . '&text=' . $encoded_title;

	$line_url = 'https://social-plugins.line.me/lineit/share?url=' . $url;

	$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
	?>

	<div class="custom-share-buttons">

		<button
			type="button"
			class="share-btn share-copy"
			data-url="<?php echo esc_url( $permalink ); ?>"
		>
			Copy
		</button>

		<a
			href="<?php echo esc_url( $x_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			class="share-btn"
		>
			𝕏
		</a>

		<a
			href="<?php echo esc_url( $line_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			class="share-btn"
		>
			LINE
		</a>

		<a
			href="<?php echo esc_url( $facebook_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			class="share-btn"
		>
			Facebook
		</a>

	</div>

	<script>
	(function () {

		'use strict';

		const copyButtons = document.querySelectorAll('.share-copy');

		copyButtons.forEach(function (button) {

			button.addEventListener('click', async function () {

				const url = this.getAttribute('data-url');
				const originalText = this.textContent;

				if (!navigator.clipboard || !window.isSecureContext) {
					this.textContent = 'Copy unavailable';

					setTimeout(() => {
						this.textContent = originalText;
					}, 2000);

					return;
				}

				try {

					await navigator.clipboard.writeText(url);

					this.textContent = 'Copied!';

				} catch (error) {

					this.textContent = 'Copy failed';

				}

				setTimeout(() => {
					this.textContent = originalText;
				}, 2000);

			});

		});

	})();
	</script>

	<?php
}
