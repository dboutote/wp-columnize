<?php
/*
Plugin Name: WP Columnize
Plugin URI: http://darrinb.com/notes/2008/wp-columnize-a-wordpress-plugin-for-creating-columns-in-posts/
Description: Easily create multiple columns within posts and pages.
Version: 1.0
Author: Darrin Boutote
Author URI: http://darrinb.com
*/
/*
Copyright 2008  Darrin Boutote  (contact : http://darrinb.com/hello/)
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


class mishWPColumnize {
	
	public function __construct() {
		add_action( 'admin_print_footer_scripts', array(&$this, 'add_quicktags'), 100 );
		add_action( 'the_content', array(&$this, 'content_filter'), 13 );
		add_shortcode( 'col-sect', array( &$this, 'shortcode_colsect' ) );
		add_shortcode( 'column', array( &$this, 'shortcode_column' ) );
	}


	/**
	 * Add a button to the Text Editor
	 */
	function add_quicktags() {
		if (wp_script_is('quicktags')){ ?>
			<script type="text/javascript" charset="utf-8">
				// <![CDATA[
				if ( typeof QTags != 'undefined' ) {
					QTags.addButton( 'db_colsect', 'col-sect', '[col-sect id="" classes=""]', '[/col-sect]', '', 'Column Section', '', '' );
					QTags.addButton( 'db_col', 'column', '[column id="" classes=""]', '[/column]', '', 'Column', '', '' );
				}
				// ]]>
			</script>		
		<?php }
	}
	
	/**
	 * Adds short codes so buttons *do* something.
	 * Matches the button ['col-sect']
	 */
	function shortcode_colsect( $atts, $content = null ) {
		$sect_id = '';
		$sect_classes = 'column-sect';
		
		extract( shortcode_atts( array(
			'id' => '',
			'classes' => ''
		), $atts ) );

		if( '' !== $id ) { 
			$sect_id = ' id="'.$id.'"'; 
		}
		
		if( '' !== $classes ) { 
			$sect_classes .= ' ' . $classes; 
		}
		
		return '<div'.$sect_id.' class="'.$sect_classes.'">' . do_shortcode( $content ) . '</div>';
	}
	
	
	/**
	 * Adds short codes so buttons *do* something.
	 * Matches the button ['column']
	 */	
	function shortcode_column( $atts, $content = null ) {
		$col_id = '';
		$col_classes = 'post-column';
		
		extract( shortcode_atts( array(
			'id' => '',
			'classes' => ''
		), $atts ) );
				
		if( '' !== $id ) { 
			$col_id = ' id="'.$id.'"'; 
		}
		
		if( '' !== $classes ) { 
			$col_classes .= ' ' . $classes; 
		}
		
		$content = wpautop( $content );
		$content = wptexturize( $content );
		return '<div'.$col_id.' class="'.$col_classes.'">' . do_shortcode( $content ) . '</div>';
		
	}

	/**
	 * Do some content filtering ( thanks to wpautop() )
	 */
	function content_filter( $col_content ) {

		/*
		 * Hitting "enter" once after closing the first [column] adds a [br /] tag
		 * This filters it it out.
		 */
		$col_content = preg_replace( '@<\/div><br \/>@', '</div>', $col_content );

		/*
		 * Hitting "enter" once after closing a [column section] forces a [p] tag at the beginning of the section
		 * This should remove the errant [p] tag and force it after the section.
		 */
		$col_content = preg_replace ( '@<p><div class="column-sect">@', '<div class="column-sect">', $col_content );

		/**
		 * The following is for backwards compatibility for versions < 0.6.3
		 */

		// Will match "<p><!--col-sect-->" OR "<p><!--col-sect--></p>"
		$col_content = preg_replace ('@((<p[^>]*?>)|(<br \/>))?<!--col-sect-->((<\/p>)|(<br \/>))?@', '<div class="column-sect">', $col_content);
		$col_content = preg_replace ('@((<p[^>]*?>)|(<br \/>))?&lt;!&#8211;col-sect&#8211;&gt;((<\/p>)|(<br />))?@', '<div class="column-sect">', $col_content);

		// Will match "<p><!--column-->" OR "<p><!--column--></p>"
		$col_content = preg_replace ('@((<p[^>]*?>)|(<br \/>))?<!--column-->((<\/p>)|(<br \/>))?@', '<div class="post-column">', $col_content);

		// Will match "<p><!--/column-->" OR "<p><!--column--></p>"
		$col_content = preg_replace ('@((<p[^>]*?>)|(<br \/>))?<!--\/column-->((<\/p>)|(<br \/>))?@', '</div>', $col_content);

		// Will match "<p><!--/col-sect-->" OR "<p><!--col-sect--></p>"
		$col_content = preg_replace ('@((<p[^>]*?>)|(<br \/>))?<!--\/col-sect-->((<\/p>)|(<br \/>))?@', '</div>', $col_content);
		$col_content = preg_replace ('@((<p[^>]*?>)|(<br \/>))?&lt;!&#8211;\/col-sect&#8211;&gt;((<\/p>)|(<br \/>))?@', '</div>', $col_content);

		return $col_content;

	}

	

}

new mishWPColumnize();

?>