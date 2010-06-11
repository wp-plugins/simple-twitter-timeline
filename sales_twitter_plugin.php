<?php
/*
Plugin Name: Simple Twitter Timeline
Plugin URI: http://www.sasakaranovic.com/projects/my-twitter-timeline-wordpress-plugin/
Description: Displays a sidebar with your latest tweets. You can define number of tweets, title and twitter username shown at your settings page. Also to display the widget on your home page please go to your widgets page and add Sale's Twitter Timeline to your sidebar.
Version: 1.0 beta
Author: Sasa Karanovic
Author URI: http://www.sasakaranovic.com/
License: GPL
*/


//Instal-UnInstall maintenence
/* Runs when plugin is activated */
register_activation_hook(__FILE__,'sale_twitter_timeline_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'sale_twitter_timeline_remove' );

function sale_twitter_timeline_install() {
	/* Creates new database field */
	add_option("sale_twitter_timeline_username", 'iSaleK', '', 'yes');
	add_option("sale_twitter_timeline_items", '15', '', 'yes');
	add_option("sale_twitter_timeline_title", 'My Twitter Timeline', '', 'yes');
}

function sale_twitter_timeline_remove() {
	/* Deletes the database field */
	delete_option('sale_twitter_timeline_username');
	delete_option('sale_twitter_timeline_items');
	delete_option('sale_twitter_timeline_title');
}




//Admin Page
if ( is_admin() ){
	
	/* Call the html code */
	add_action('admin_menu', 'sale_twitter_timeline_admin_menu');
	
	function sale_twitter_timeline_admin_menu() {
		add_options_page('Sale\'s Twitter Plugin', 'Sale\'s Twitter Plugin', 'administrator', 'sk-twitter-plugin', 'sale_twitter_timeline_html_admin_page');
	}
}


function sale_twitter_timeline_html_admin_page() {
	?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>

  <h2>Sasa Karanovic's Twitter Timeline Plugin</h2>
    
<br />
<br />
  <h3 class="title">Configure your Twitter Timeline Plugin</h3>
	
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
	
	<table width="510">
        <tr valign="top">
          <th valign="middle" scope="row">Widget Title</th>
          <td valign="middle"><input name="sale_twitter_timeline_title" type="text" id="sale_twitter_timeline_title" value="<?php echo get_option('sale_twitter_timeline_title'); ?>" />
          <br />
          Title of your sidebar widget
          </td>
        </tr>
        <tr valign="top">
          <th valign="middle" scope="row">&nbsp;</th>
          <td valign="middle">&nbsp;</td>
        </tr>
        <tr valign="top">
        <th width="92" valign="middle" scope="row">Twitter Username</th>
        <td width="406" valign="middle">
            <input name="sale_twitter_timeline_username" type="text" id="sale_twitter_timeline_username" value="<?php echo get_option('sale_twitter_timeline_username'); ?>" />
          <br />
          Your username on Twitter
          </td>
	</tr>
	<tr valign="top">
	  <th valign="middle" scope="row">&nbsp;</th>
	  <td valign="middle">&nbsp;</td>
	  </tr>
	<tr valign="top">
	  <th valign="middle" scope="row">Timeline Items</th>
        <td width="406" valign="middle">
            <input name="sale_twitter_timeline_items" type="text" id="sale_twitter_timeline_items" value="<?php echo get_option('sale_twitter_timeline_items'); ?>" />
          <br />
        How many items would you like to display on your timeline </td>
	  </tr>
	<tr valign="top">
	  <th valign="middle" scope="row">&nbsp;</th>
	  <td valign="middle">&nbsp;</td>
	  </tr>
	<tr valign="top">
	  <th valign="middle" scope="row">&nbsp;</th>
	  <td valign="middle">&nbsp;</td>
	  </tr>
	<tr valign="top">
	  <th colspan="2" valign="middle" scope="row"><input type="submit" value="<?php _e('Save Changes') ?>" /></th>
	  </tr>
	<tr valign="top">
	  <th colspan="2" valign="middle" scope="row">&nbsp;</th>
	  </tr>
	<tr valign="top">
	  <td colspan="2" align="center" valign="middle" scope="row"><a href="http://twitter.com/?status=I+have+installed+new+SK+Twitter+Timeline+plugin+on+my+blog.+Check+it+out+here+http://is.gd/cICJc+%23wordpress+%23plugin" target="_blank">If you like this plugin please tweet this message to let me know. Thank you!</a></td>
	  </tr>
	</table>
	
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="sale_twitter_timeline_username,sale_twitter_timeline_items,sale_twitter_timeline_title" />
	
	<p>&nbsp;</p>
	
	</form>
	</div>
	<?php
}


function sales_twitter_timeline_display() {
	//
	$twitter_username = get_option('sale_twitter_timeline_username');
	$timeline_items = get_option('sale_twitter_timeline_items');
	$url = "http://api.twitter.com/1/statuses/user_timeline.json?id=$twitter_username&count=$timeline_items";
	
	if ($stream = fopen($url, 'r')) {
		// print all the page starting at the offset 10
		$twitter_timeline = stream_get_contents($stream);
		fclose($stream);

		$timeline = json_decode($twitter_timeline, TRUE);
		
		?>
        <div style="margin:5px 5px;">
        <?php
		
		foreach($timeline as $item) {
			?>
            <p style="margin:6px 0;"><a href="http://www.twitter.com/<?= $item['user']['screen_name'] ?>" target="_blank" title="<?= $item['user']['name'] ?> from <?= $item['user']['location'] ?>"><?= $item['user']['screen_name'] ?></a> <?= $item['text']?></p>
        <?php
		}
		
		?>
        </div>
        <?php
	}
}


//Sidebar
function sales_twitter_timeline_widget($args) {
  extract($args);
  echo $before_widget;
  echo $before_title;?> <?=get_option('sale_twitter_timeline_title')?> <?php echo $after_title;
  if(function_exists(sales_twitter_timeline_display)) {
	  sales_twitter_timeline_display();
  }
  echo $after_widget;
}
 
function init_sales_twitter_timeline(){
	register_sidebar_widget("Sale's Twitter Timeline", "sales_twitter_timeline_widget");     
}
 
add_action("plugins_loaded", "init_sales_twitter_timeline");

?>
