<?php
/*
Plugin Name: WP Columnize
Plugin URI: http://darrinb.com/notes/2008/wp-columnize-a-wordpress-plugin-for-creating-columns-in-posts/
Description: Easily create multiple columns within posts.
Version: 0.6.5
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


// Init function to insert (install) button options into Options Table/
function mshwpcl_install() {
	global $wpdb;

	if ( get_option( 'mishWPColumnize') == '' ) {
		$name        = 'mishWPColumnize';
		$value       = 'a:1:{s:7:"buttons";a:2:{i:0;a:3:{s:4:"text";s:8:"col-sect";s:5:"start";s:10:"[col-sect]";s:3:"end";s:11:"[/col-sect]";}i:1;a:3:{s:4:"text";s:6:"column";s:5:"start";s:8:"[column]";s:3:"end";s:9:"[/column]";}}}';
		$autoload    = 'yes';
		$wpdb->query("INSERT INTO $wpdb->options (option_name, option_value, autoload) VALUES ('$name', '$value', '$autoload')");
	}
}

// Init function to delete (uninstall) button options from Options Table
function mshwpcl_uninstall() {
	delete_option( 'mishWPColumnize' );
}

/*
 * Loads the buttons only if user is on "post.php", "page.php",
 * "post-new.php", "page-new.php" or "comment.php".
 */
if ( strpos( $_SERVER['REQUEST_URI'], 'post.php' ) || strpos( $_SERVER['REQUEST_URI'], 'post-new.php' ) || strpos( $_SERVER['REQUEST_URI'], 'page-new.php' ) || strpos( $_SERVER['REQUEST_URI'], 'page.php' ) || strpos( $_SERVER['REQUEST_URI'], 'comment.php' ) )
{
	add_action( 'admin_footer', 'mshwpcl_addbtns' );

	// adds buttons to the Toolbar in the HTML editor
	function mshwpcl_addbtns() {

		// Checks if there's anything in the Options Table
		$o = get_option( 'mishWPColumnize' );

		// If there is
		if ( count( $o['buttons'] ) > 0 ) {
			echo '
			<script type="text/javascript">
			<!--
			if (mshwpclToolbar = document.getElementById("ed_toolbar")) {
				var mshwpclNr, mshwpclBut, mshwpclStart, mshwpclEnd;
				';
				for ($i = 0; $i < count($o['buttons']); $i++) {
					$b = $o['buttons'][$i];
					$txt = html_entity_decode(stripslashes($b['txt']), ENT_COMPAT, get_option('blog_charset'));
					$text = stripslashes($b['text']);
					$b['text'] = stripslashes($b['text']);
					$start = preg_replace('![\n\r]+!', "\\n", $b['start']);
					$start = str_replace("'", "\'", $start);
					$end = preg_replace('![\n\r]+!', "\\n", $b['end']);
					$end = str_replace("'", "\'", $end);
					echo '
					mshwpclStart = \'' . $start . '\';
					mshwpclEnd = \'' . $end . '\';
					mshwpclNr = edButtons.length;
					edButtons[mshwpclNr] = new edButton(\'ed_mshwpcl' . $i . '\', \'' . $b['txt'] . '\', mshwpclStart, mshwpclEnd,\'\');
					var mshwpclBut = mshwpclToolbar.lastChild;
					while (mshwpclBut.nodeType != 1) {
						mshwpclBut = mshwpclBut.previousSibling;
					}
					mshwpclBut = mshwpclBut.cloneNode(true);
					mshwpclToolbar.appendChild(mshwpclBut);
					mshwpclBut.value = \'' . $b['text'] . '\';
					mshwpclBut.title = mshwpclNr;
					mshwpclBut.onclick = function () {edInsertTag(edCanvas, parseInt(this.title));}
					mshwpclBut.id = "ed_mshwpcl' . $i .'";
					';
					}
				echo '
			}
			//-->
			</script>
			';
		}
	} // End mshwpcl_addbtns()
} // End if

// Triggers the buttons' install function upon plugin activation
if ( function_exists( 'register_activation_hook' ) )
	register_activation_hook( __FILE__, 'mshwpcl_install' );

// Triggers the buttons' uninstall function upon plugin deactivation
if ( function_exists('register_uninstall_hook') )
	register_uninstall_hook(__FILE__, 'mshwpcl_uninstall');

/*
 * Adds short codes so buttons *do* something.
 * Matches the button ['col-sect'] created on plugin installation.
 */
function columns_shortcode( $atts, $content = null ) {
	$content = wpautop( $content );
	$content = wptexturize( $content );
	return '<div class="column-sect">' . PHP_EOL . do_shortcode( $content ) . '</div>';
}
add_shortcode( 'col-sect', 'columns_shortcode' );

/*
 * Adds short codes so buttons *do* something.
 * Matches the button ['post-column'] created on plugin installation.
 */
function column_shortcode( $atts, $content = null ) {
	$content = wpautop( $content );
	$content = wptexturize( $content );
	return '<div class="post-column">' . PHP_EOL . do_shortcode( $content ) . '</div>';
}
add_shortcode( 'column', 'column_shortcode' );


// Do some content filtering ( thanks to wpautop() )
function mshwpcl_filter( $col_content ) {

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

	/*
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

add_filter( 'the_content', 'mshwpcl_filter', 13 );

?>