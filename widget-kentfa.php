<?php

/*
		Plugin Name: KentFA Feed
		Plugin URI: http://www.kentfa.com/
		Description: A widget plugin that displays the Kent FA RSS feed.
		Version: 1.3
		Author: Paul O'Brien
		Author URI: http://twitter.com/@paulobrien
*/

class wp_kentfa_plugin extends WP_Widget {

	function wp_kentfa_plugin() {
		$options = array( 'description' => __( "Display the KentFA RSS feed", 'wp_kentfa_plugin' ) );
        parent::WP_Widget(false, $name = __('KentFA Feed', 'wp_kentfa_plugin'), $options );
    }
	
	function form($instance) {
	
		$defaults = array('showlinks' => false, 'widgettitle' => 'Grassroots Football News<br /><span>in association with the Kent FA</span>' );  
		$instance = wp_parse_args( (array) $instance, $defaults );
	
		$showlinks = esc_attr($instance['showlinks']);
		$widgettitle = esc_attr($instance['widgettitle']);
		
		$showlinksid = $this->get_field_id('showlinks');
		$showlinksname = $this->get_field_name('showlinks');

		$widgettitleid = $this->get_field_id('widgettitle');
		$widgettitlename = $this->get_field_name('widgettitle');

		$checked = checked(1, $showlinks, false);

		echo("<p>");
		echo("<input type='checkbox' id='$showlinksid' name='$showlinksname' value='1' $checked />"); 
		echo("<label for='$showlinksid'> Show Links</label>");
		echo("</p>");
		echo("<p>");
		echo("<input type='text' id='$widgettitleid' name='$widgettitlename' value='$widgettitle' />"); 
		echo("<label for='$widgettitleid'> Widget Title</label>");
		echo("</p>");
    }
	
	function update($new_instance, $old_instance) {
	    $instance = $old_instance;
	    $instance['showlinks'] = strip_tags($new_instance['showlinks']);
	    $instance['widgettitle'] = $new_instance['widgettitle'];
	    return $instance;
	}
	
	function widget($args, $instance) {
	
		extract($args);
		
		$showlinks = $instance['showlinks'];
		$widgettitle = $instance['widgettitle'];

		$plugindirpath = plugin_dir_path( __FILE__ );
		$plugindirurl = plugin_dir_url( __FILE__ );
		
		include_once( ABSPATH . WPINC . '/feed.php' );
		$rss = fetch_feed( 'http://www.kentfa.com/rss/news' );

		if (!is_wp_error($rss))
		{
			$maxitems = $rss->get_item_quantity( 5 ); 
			$rss_items = $rss->get_items( 0, $maxitems );
			
			wp_enqueue_style("kentfa", "$plugindirurl/widget-kentfa.css"); 
		
			echo("<center>");
			echo("<div id='kentFaFeed'>");
			echo("<div id='newsPlacer'>");
			
			foreach ($rss_items as $item)
			{
				$item_title=$item->get_title();
				$item_link=$item->get_permalink();
				echo ("<p class='feedItem'><a href='" . $item_link . "' title='" . $item_title . "' target='_blank'>" . $item_title . "</a></p>");
			}

			echo("</div>");
			echo("<p class='feedHeading'>$widgettitle</p>");
			echo("<p class='feedLinks'>");
			if ($showlinks == true)
			{
				echo("<a href='http://www.kentfa.com' title='Kent FA' class='feedLink' target='_blank'>www.kentfa.com</a> | <a href='http://www.twitter.com/kentfa' title='Kent FA on Twitter' class='feedLink' target='_blank'>@kentfa</a>");
			}
			echo("</p>");
			echo("</div>");
			echo("</center>");
		}
	}
}

function return_900($seconds)
{
  return 900;
}

function register_wp_kentfa_plugin() {
	register_widget( 'wp_kentfa_plugin' );
}

add_filter( 'wp_feed_cache_transient_lifetime' , 'return_900' );

add_action('widgets_init', 'register_wp_kentfa_plugin');

?>