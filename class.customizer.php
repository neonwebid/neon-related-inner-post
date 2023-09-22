<?php

namespace NeonRelatedInnerPosts;

class Customizer {

	public function __construct() {
		add_action( 'customize_register', [ $this, 'customizer' ] );
	}

	public function customizer( $wp_customize ) {
		$wp_customize->add_section( 'neon_related_inner_posts', array(
			'title'    => 'Pengaturan Related Inner Pos',
			'priority' => 30,
		) );

		// Field untuk menampilkan jumlah terkait pos
		$wp_customize->add_setting( 'neon_show_related_post', array(
			'default' => 3,
			'type'    => 'option',
		) );

		$wp_customize->add_control( 'neon_show_related_post', array(
			'label'   => 'Tampilkan Berapa Post Terkait',
			'section' => 'neon_related_inner_posts',
			'type'    => 'number',
		) );

		// Field untuk menampilkan thumbnail
		$wp_customize->add_setting( 'neon_display_thumbnail', array(
			'default' => true,
			'type'    => 'option',
		) );

		$wp_customize->add_control( 'neon_display_thumbnail', array(
			'label'   => 'Tampilkan Thumbnail',
			'section' => 'neon_related_inner_posts',
			'type'    => 'checkbox',
		) );

		// Field untuk judul terkait pos
		$wp_customize->add_setting( 'neon_title_related_posts', array(
			'default' => 'Artikel Terkait',
			'type'    => 'option',
		) );

		$wp_customize->add_control( 'neon_title_related_posts', array(
			'label'   => 'Judul Terkait Pos',
			'section' => 'neon_related_inner_posts',
			'type'    => 'text',
		) );

		// Field untuk auto insert
		$wp_customize->add_setting( 'neon_auto_insert_related_posts', array(
			'default' => false,
			'type'    => 'option',
		) );

		$wp_customize->add_control( 'neon_auto_insert_related_posts', array(
			'label'   => 'Auto Insert Related Posts',
			'section' => 'neon_related_inner_posts',
			'type'    => 'checkbox',
		) );

		$wp_customize->add_setting( 'neon_auto_insert_after_paragraph', array(
			'default' => 2,
			'type'    => 'option',
		) );

		$choices = [];
		for($i = 1; $i <= 10; $i++) {
			$choices[$i] = 'Paragraph ' . $i;
		}

		$wp_customize->add_control( 'neon_auto_insert_after_paragraph', array(
			'label'   => 'Auto Insert After Paragraph',
			'section' => 'neon_related_inner_posts',
			'type'    => 'select',
			'choices' => $choices,
		) );


		$post_types        = get_post_types( array( 'public' => true ), 'objects' );
		$post_type_choices = array();
		foreach ( $post_types as $post_type ) {
			$post_type_choices[ $post_type->name ] = $post_type->label;
		}

		$wp_customize->add_setting( 'neon_selected_post_types', array(
			'default' => array( 'post' ),
			'type'    => 'option',
		) );

		$wp_customize->add_control( 'neon_selected_post_types', array(
			'label'   => 'Pilih Jenis Posting',
			'section' => 'neon_related_inner_posts',
			'type'    => 'checkbox',
			'choices' => $post_type_choices,
		) );
	}
}

new Customizer();