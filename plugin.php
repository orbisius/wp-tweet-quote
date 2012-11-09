<?php
/*
Plugin Name: TweetQuote
Plugin URI: http://orbisius.com
Description: This WordPress plugin allows your visitors to tweet a phrase/quote from your article.
Version: 1.0.0
Author: Orbisius.com | Svetoslav Marinov (Slavi)
Author URI: http://orbisius.com
*/

/*  Copyright 2012 Svetoslav Marinov (Slavi) <slavi@orbisius.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Set up plugin
add_action('init', 'orbisius_tweet_phrase_init');
add_action('wp_head', 'orbisius_tweet_phrase_header');
add_action('wp_footer', 'orbisius_tweet_phrase_footer', 1000); // be the last in the footer
add_action('wp_print_styles', 'orbisius_tweet_phrase_add_css');
add_action('admin_menu', 'orbisius_tweet_phrase_setup_admin');

function orbisius_tweet_phrase_init() {
	// scripts
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-dialog');
	
	// styles
	wp_enqueue_style('tweet_phrase-jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css'); 
	wp_enqueue_style('wp-jquery-ui-dialog');	
}

/**
 * @package Permalinks to Category/Permalinks
 * @since 1.0
 *
 * Searches through posts to see if any matches the REQUEST_URI.
 * Also searches tags
 */
function orbisius_tweet_phrase() {
	
}

// Add the ? settings link in Plugins page very good
function orbisius_tweet_phrase_add_plugin_settings_link($links, $file) {
	if ($file == plugin_basename(__FILE__)) {
		$prefix = 'options-general.php?page=' . dirname(plugin_basename(__FILE__));

		$settings_link = "<a href=\"{$prefix}/plugin.php\">" . __("Settings", 'tweet_phrase') . '</a>';

		array_unshift($links, $settings_link);
	}

	return $links;
}

function orbisius_tweet_phrase_add_css() {

	/*$myStyleFile = WP_PLUGIN_URL . '/style/sl_forms.css';
	
	if (file_exists($myStyleFile)) {
		wp_register_style('orbisius_tweet_phrase_css', $myStyleFile);
		wp_enqueue_style('orbisius_tweet_phrase_css');
	}*/
}

/**
 * @package Permalinks to Category/Permalinks
 * @since 1.0
 *
 * Searches through posts to see if any matches the REQUEST_URI.
 * Also searches tags
 */
function orbisius_tweet_phrase_header() {
	echo <<<HEADER_EOF
	<!-- tweet_phrase -->
	<style>
	.tweet_phrase_selected { background: #ff9 !important; }
	#tweet_selection_link {
		width: 95%;
		color: green;
		margin: 0 auto;
		padding: 5px;
		border: 1px solid green;
		font-weight:bolder;
	}
	</style>
	<script type="text/javascript">
		jQuery(document).ready(function() {		
			// setup dialog
			jQuery("#tweet_phrase_dialog").dialog({
				autoOpen: false,
				modal: true,
				open: function() {
					jQuery('.tweet_phrase_loading').remove();
					var sel = jQuery('.tweet_phrase_selected').text();
				
					if (1) { // always include a link to the article.
						var link = document.location.href;

						// rm any anchor stuff
						link = link.replace(/\#.*/g, '');
						sel += ' ' + link;						
					}
				
					jQuery('#tweet_phrase_len').html(sel.length);					
					jQuery('#tweet_phrase_len').css('color', sel.length > 140 ? 'red' : 'green');
					
					jQuery('#tweet_selection_link').prop('href', 'http://twitter.com/home?status=' + escape(sel));
					jQuery('#tweet_selection_link').click(function () {
						jQuery(this).after('<div class="tweet_phrase_loading">Contacting Twitter...</div>');
						
						window.setTimeout(function() {							
							jQuery("#tweet_phrase_dialog").dialog("close");
						}, 3000);						
						
						return true;
					});
				},
				close: function() {
					jQuery('.tweet_phrase_selected').removeClass('tweet_phrase_selected');
					jQuery("#tweet_phrase_dialog").dialog("close");
				}
			});
	
			jQuery('.post span,.post p,.post div,.post code').click(function(e) {
				e.preventDefault();
				
				jQuery(this).toggleClass('tweet_phrase_selected');
				
				if (jQuery(this).hasClass('tweet_phrase_selected')) {
					var sentences = jQuery('.tweet_phrase_selected', this).map(function(i,t){
						return jQuery(t).text();
					}).get().join(', ');
					
					jQuery("#tweet_phrase_dialog").dialog("open");
				}
			});
		});
	</script>
	<!-- /tweet_phrase -->
HEADER_EOF;

}

/**
* adds some HTML comments in the page so people would know that this plugin powers their site.
*/
function orbisius_tweet_phrase_footer() {
    printf(PHP_EOL . PHP_EOL . '<!-- ' . PHP_EOL . ' Powered by xyz Plugin | Author URL: http://orbisius.com ' . PHP_EOL . '-->' . PHP_EOL . PHP_EOL);
	
	echo <<<DLG_EOF
	<div id="tweet_phrase_dialog" title="TweetPhrase">
		<small>
			<span class="tweet_phrase_intro">
				<a href="http://orbisius.com/tweetphrase" target="_blank" title="Visit TweetPhrase Site [new window/tab]"><strong>TweetPhrase</strong></a> allows you to select a phrase then tweet it. You will be able to edit the text before posting it on Twitter.				
				<br/>
				<br/>
			</span>
			
			<span id="tweet_phrase_len">0</span> char(s) (includes the link to the article).<br/>
		</small>		
		<br/>
		<div>
			<a id="tweet_selection_link" href="javascript:void(0);" target="_blank" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">Tweet</a>				
		</div>			
	</div>	
	
DLG_EOF;

}

/**
 * Set up administration
 *
 * @package Permalinks to Category/Permalinks
 * @since 0.1
 */
function orbisius_tweet_phrase_setup_admin() {
	add_options_page('TweetQuote', 'TweetQuote', 5, __FILE__, 'orbisius_tweet_phrase_options_page');

	// when plugins are show add a settings link near my plugin for a quick access to the settings page.
	add_filter('plugin_action_links', 'orbisius_tweet_phrase_add_plugin_settings_link', 10, 2);
}

/**
 * Options page
 *
 * @package Permalinks to Category/Permalinks
 * @since 1.0
 */
function orbisius_tweet_phrase_options_page() {
    $current_user = wp_get_current_user();
	
	$link = 'http://orbisius.us2.list-manage.com/subscribe?u=005070a78d0e52a7b567e96df&id=1b83cd2093'; // http://eepurl.com/guNzr
	
	$params = array(
		'MERGE0' => $current_user->user_email,
		'MERGE1' => $current_user->user_firstname,
		'MERGE2' => $current_user->user_lastname,

		'MERGE3' => 'WP TweetQuote',
		'MERGE4' => 'Settings',
	);
	
	$link .= '&' . http_build_query($params);
	
	?>
	<div class="wrap">
        <h2>TweetQuote</h2>
        <p>This plugin is mandatory if you want your visitors to tweet some quotes of your articles. After the user clicks on the article a portion of it gets selected and offerred to be tweeted. The user can edit the message befere tweeting it. A link to the original article is included in the tweet too.
        </p>
		
		<p>Currently, the plugin does not require any configuration options.
        </p>

        <h2>Join the Mailing List</h2>
        <p>
            Get the latest news and updates about this and future cool <a href="http://profiles.wordpress.org/lordspace/"
                                                                            target="_blank" title="Opens a page with the pugins we developed. [New Window/Tab]">plugins we develop</a>.
        </p>

        <p>
            <!-- // MAILCHIMP SUBSCRIBE CODE \\ -->
            1) <a href="<?php echo $link;?>" target="_blank">Subscribe to our newsletter</a> (your info will be prefilled but you'll be able to edit it)
            <!-- \\ MAILCHIMP SUBSCRIBE CODE // -->
        </p>
        <p>OR</p>
        <p>
            2) Subscribe using our QR code. [Scan it with your mobile device].<br/>
            <img src="<?php echo plugin_dir_url(__FILE__); ?>/i/guNzr.qr.2.png" alt="" />
        </p>

        <h2>Support</h2>
        <p>
            Suggest ideas to <a href="mailto:help@orbisius.com?subject=TweetQuote Contact"
                                            target="_blank">help@orbisius.com</a>
            or visit our web site <a href="http://orbisius.com" target="_blank">orbisius.com</a> and call.
        </p>

        <?php 
            $app_link = 'http://wordpress.org/extend/plugins/permalinks-to-categorypermalinks/';
            $app_title = 'TweetQuote';
            $app_descr = 'This WordPress plugin allows your visitors to tweet a phrase/quote from your article.';
        ?>
        <h2>Share</h2>
        <p>
            <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                <a class="addthis_button_facebook" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_twitter" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_google_plusone" g:plusone:count="false" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_linkedin" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_email" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_myspace" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_google" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_digg" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_delicious" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_stumbleupon" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_tumblr" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_favorites" addthis:url="<?php echo $app_link?>" addthis:title="<?php echo $app_title?>" addthis:description="<?php echo $app_descr?>"></a>
                <a class="addthis_button_compact"></a>
            </div>
            <!-- The JS code is in the footer -->

            <script type="text/javascript">
            var addthis_config = {"data_track_clickback":true};
            var addthis_share = {
                templates: { twitter: 'Check out {{title}} @ {{lurl}} (from @orbisius)' }
            }
            </script>
            <!-- AddThis Button START part2 -->
            <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=lordspace"></script>
            <!-- AddThis Button END part2 -->
        </p>

	</div>
	<?php
}

