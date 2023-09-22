<?php

namespace NeonRelatedInnerPosts;

use WP_Query;

class RelatedInnerPosts {

	private $neon_show_related_post;

	private $neon_selected_post_types;
	private $neon_display_thumbnail;
	private $neon_title_related_posts;

	private $neon_auto_insert_after_paragraph;


	public function __construct() {
		$this->neon_selected_post_types = get_option( 'selected_post_types', [ 'post' ] );
		$this->neon_show_related_post = get_option( 'neon_show_related_post', 3 );
		$this->neon_display_thumbnail = get_option( 'neon_display_thumbnail', true );
		$this->neon_title_related_posts = get_option( 'neon_title_related_posts', 'Read Also' );

		add_shortcode( 'neon_related_inner_post', [ $this, 'shortcode' ] );
		add_shortcode( 'neon_related_inner_post_by_id', [ $this, 'shortcodeById' ] );

		if ( get_option('neon_auto_insert_related_posts', false) ) {
			$this->neon_auto_insert_after_paragraph = get_option('neon_auto_insert_after_paragraph', 2);

			$this->autoInsert();
		}
	}

	public function shortcode( $atts ) {
		global $post;


		$related_html = '';

		if ( $this->neon_selected_post_types && is_singular( $this->neon_selected_post_types ) ) {

			$parameter = shortcode_atts( array(
				'show'      => $this->neon_show_related_post,
				'thumbnail' => $this->neon_display_thumbnail,
				'title'     => $this->neon_title_related_posts,
				'tax'       => 'category'
			), $atts );

			$_terms = get_the_terms( $post->ID, $parameter['tax'] );

			$related_terms = [];
			if ( $_terms && ! is_wp_error( $_terms ) ) {
				foreach ( $_terms as $term ) {
					$related_terms[] = $term->slug;
				}
			}

			$related_posts = new WP_Query( [
				'post_type'      => $post->post_type,
				'posts_per_page' => $parameter['limit'],
				'post__not_in'   => [ $post->ID ],
				'tax_query'      => array(
					array(
						'taxonomy' => $parameter['tax'],
						'field'    => 'slug',
						'terms'    => $related_terms,
					),
				),
			] );

			if ( $related_posts->have_posts() ) {
				$related_html = $this->loopTemplate($related_posts, $parameter);
			}

		}

		return $related_html;
	}

	public function shortcodeById( $atts ) {
		global $post;

		$post_type = get_option( 'selected_post_types', 'post' );

		$related_html = '';

		if ( $this->neon_selected_post_types && is_singular( $this->neon_selected_post_types ) ) {

			$parameter = shortcode_atts( array(
				'show'      => $this->neon_show_related_post,
				'thumbnail' => $this->neon_display_thumbnail,
				'title'     => $this->neon_title_related_posts,
				'ids'       => ''
			), $atts );

			$related_posts = new WP_Query( [
				'post_type'      => $post->post_type,
				'post__in'       => implode(', ', $parameter['ids']),
				'post__not_in'   => [ $post->ID ],
			] );

			if ( $related_posts->have_posts() ) {
				$related_html = $this->loopTemplate($related_posts, $parameter);
			}

		}

		return $related_html;

	}

	private function loopTemplate( $related_posts, $parameter) {
		$default_thumbnail = '//placehold.co/150';

		$related_html = '<div class="neon-related-inner-post">';
		$related_html .= '<div class="neon-related-inner-post-title">' . $parameter['title'] . '</div>';

		while ( $related_posts->have_posts() ) {
			$related_posts->the_post();
			$title = get_the_title();

			if ( $parameter['thumbnail'] ) {
				$related_html .= '<div class="thumbnail">';
				if ( has_post_thumbnail() ) {
					$related_html .= get_the_post_thumbnail( get_the_ID(), 'thumbanil' );
				} else {
					$related_html .= sprintf( '<img src="%s" alt="%s" title="%s">', $default_thumbnail, $title, $title );
				}
				$related_html .= '</div>';
			}

			$related_html .= sprintf( '<div class="title"><a href="%s">%s</a></div>', get_permalink(), $title );
		}

		$related_html .= '</div>';

		wp_reset_postdata();

		return $related_html;
	}

	public function autoInsert() {
		add_filter('the_content', [$this, 'parseParagraph']);
	}

	public function parseParagraph($content) {
		global $post;

		$_content = '';
		$n        = 0;
		$blocks   = parse_blocks( $post->post_content );

		if ( $blocks ) {
			foreach ( $blocks as $block ) {
				if ( ! empty( $block['blockName'] ) && $block['blockName'] == 'core/paragraph' ) {
					$n ++;
				}

				$_content .= render_block( $block );
				if ( $n == $this->neon_auto_insert_after_paragraph ) {
					$_content .= do_shortcode('[neon_related_inner_post]');
				}

			}
		} else {
			$paragraphs = explode( '</p>', $post->post_content );
			foreach ( $paragraphs as $paragraph ) {

				$_content .= $paragraph . '</p>';
				if ( $n == $this->neon_auto_insert_after_paragraph ) {
					$_content .= do_shortcode('[neon_related_inner_post]');
				}

				$n ++;
			}
		}

		return $_content;
	}
}
