<?php
/*
Plugin Name: NEON Related Inline Posts
Plugin URI: http://neon.web.id/
Description: Plugin untuk menampilkan related post
Author: NEON Web Developer
Version: 1.0
Author URI: http://neon.web.id/
*/

use NeonRelatedInnerPosts\RelatedInnerPosts;

require_once __DIR__ . '/class.customizer.php';
require_once __DIR__ . '/class.related-posts.php';

new RelatedInnerPosts();