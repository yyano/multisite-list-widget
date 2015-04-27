<?php
/*
  Plugin Name: MultiSite List Widget
  Version: 0.1
  Description: List of Multisite blogs.
  Author: YANO Yasuhiro
  Author URI: https://plus.google.com/u/0/+YANOYasuhiro/
  Plugin URI: https://github.com/yyano/multisite-list-widget
  Text Domain: multisite-list-widget
  Domain Path: /languages
 */

class WP_Widget_MultiSiteList extends WP_Widget {

	const TEXT_DOMAIN = 'multisite-list-widget';

	public function __construct() {
		$widget_ops = array(
			'description' => __( "List of Multisite blogs." )
		);
		parent::__construct( 'MultiSite_List_Widget', esc_attr( __( 'MultiSite List Widget', self::TEXT_DOMAIN ) ),
															  $widget_ops );
	}

	public function form( $instance ) {
		if ( !is_multisite() ) {
			printf( '<p><font color="RED">%s</font></p>', esc_attr( __( 'THIS SITE IS NOT MULTI-SITE.', self::TEXT_DOMAIN ) ) );
		}

		$this->setFormField( 'Title:', 'title', $instance['title'] );
		$this->setFormField( 'CSS Class name (ul):', 'class_ul', $instance['class_ul'] );
		$this->setFormField( 'CSS Class name (li):', 'class_li', $instance['class_li'] );
	}

	private function setFormField( $Title, $Param, $InValue ) {
		if ( isset( $InValue ) && $InValue ) {
			$value = $InValue;
		} else {
			$value = '';
		}

		printf( '<p>%s<br />', esc_attr( __( $Title, self::TEXT_DOMAIN ) ) );
		printf( '<input type="text" id="%s" name="%s" value="%s">', $this->get_field_id( $Param ),
																				   $this->get_field_name( $Param ), esc_attr( $value ) );
		echo '</p>';
	}

	function widget( $args, $instance ) {
		echo $args['before_widget'];

		echo $args['before_title'];
		echo esc_html( $instance['title'] );
		echo $args['after_title'];

		if ( isset( $instance['class_ul'] ) && $instance['class_ul'] ) {
			printf( '<ul class="%s">', esc_html( $instance['class_ul'] ) );
		} else {
			echo '<ul>';
		}

		if ( isset( $instance['class_li'] ) && $instance['class_li'] ) {
			$TagLi = sprintf( '<li class="%s">', esc_html( $instance['class_li'] ) );
		} else {
			$TagLi = '<li>';
		}

		if ( is_multisite() ) {
			//Multisite
			$mySites = ( wp_get_sites( $args ) );

			foreach ( $mySites as $blog ) {
				switch_to_blog( $blog['blog_id'] );

				printf( '%s<a href="%s">%s</a></li>' . "\r\n", $TagLi, home_url(), get_option( 'blogname' ) );
				restore_current_blog();
			}
		} else {
			//not Multisite
			printf( '%s<a href="%s">%s</a></li>' . "\r\n", $TagLi, home_url(), get_option( 'blogname' ) );
		}

		echo '</ul>';
		echo $args['after_widget'];
	}

}

function WP_Widget_MultiSiteList_Init() {
	register_widget( 'WP_Widget_MultiSiteList' );
}

add_action( 'widgets_init', 'WP_Widget_MultiSiteList_Init' );
