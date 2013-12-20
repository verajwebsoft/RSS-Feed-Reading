<?php

/*
Plugin Name:ITF RSS Feed Reader
Description: This plug-in will scroll the RSS feed title vertically in the widget, admin can add/update the RSS link & style via widget management.
Author: Virtual Employee
Version: 1.0
Plugin URI: http://www.virtualemployee.com/
Author URI: http://www.virtualemployee.com/
*/

function gVEmp_rss()
{
	
	?>
	
	<?php
	if(get_option('gVEmp_rssfeed_url') <> "")
	{
		$url = get_option('gVEmp_rssfeed_url');
	}
	else
	{
		$url = "http://wordpress.org/development/feed/";
		
	}
	
	$totalres = get_option('gVEmp_rssfeed_numberfeed');
	
	$xml = "";
	$rssscroll = "";
	$cnt=0;
	$f = fopen( $url, 'r' );
	while( $data = fread( $f, 4096 ) ) { $xml .= $data; }
	fclose( $f );
	
	$rrr = simplexml_load_string($xml);
	if(isset($rrr->channel->item))
	{
		$rssscroll ="<ul>";
		$rows="1";
		foreach($rrr->channel->item as $kk=>$itfrssfeeds)
		{
		
		if($totalres!="0" and $totalres == $rows) break;
		$rssscroll .="<li>";
		$rssscroll .="<a href='".$itfrssfeeds->link."' target='_blank'><span>".date("h:m",strtotime($itfrssfeeds->pubDate))."</span> ".$itfrssfeeds->title."<span>&gt;</span></a>";
		$rssscroll .="</li>";
		$rows=$rows+1;
			
		}
		$rssscroll .="</ul>";
		
	} else
	{
		$rssscroll = get_option('gVEmp_rssfeed_noannouncement');
	}
	echo $rssscroll;
	?>
	
	<?php
}

function gVEmp_rssfeed_install() 
{
	add_option('gVEmp_rssfeed_title', "RSS News");
	add_option('gVEmp_rssfeed_numberfeed', '10');
	add_option('gVEmp_rssfeed_noannouncement', 'No content available');
	$rss2_url = get_option('home'). "/?feed=rss2";
	add_option('gVEmp_rssfeed_url', $rss2_url);
}

function gVEmp_rssfeed_widget($args) 
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('gVEmp_rssfeed_title');
	echo $after_title;
	gVEmp_rss();
	echo $after_widget;
}
	
function gVEmp_rssfeed_control() 
{
	$gVEmp_rssfeed_title = get_option('gVEmp_rssfeed_title');
	$gVEmp_rssfeed_numberfeed = get_option('gVEmp_rssfeed_numberfeed');
	$gVEmp_rssfeed_noannouncement = get_option('gVEmp_rssfeed_noannouncement');
	$gVEmp_rssfeed_url = get_option('gVEmp_rssfeed_url');
	
	if (@$_POST['gVEmp_rssfeed_submit']) 
	{	
		$gVEmp_rssfeed_title = stripslashes($_POST['gVEmp_rssfeed_title']);
		
		$gVEmp_rssfeed_numberfeed = stripslashes($_POST['gVEmp_rssfeed_numberfeed']);
		$gVEmp_rssfeed_noannouncement = stripslashes($_POST['gVEmp_rssfeed_noannouncement']);
		$gVEmp_rssfeed_url = stripslashes($_POST['gVEmp_rssfeed_url']);
		
		update_option('gVEmp_rssfeed_title', $gVEmp_rssfeed_title );
		update_option('gVEmp_rssfeed_numberfeed', $gVEmp_rssfeed_numberfeed );
		update_option('gVEmp_rssfeed_noannouncement', $gVEmp_rssfeed_noannouncement );
		update_option('gVEmp_rssfeed_url', $gVEmp_rssfeed_url );
	}
		?>
		<table width='560' border='0' cellspacing='0' cellpadding='3'>
		  <tr>
			<td colspan="3">Enter URL</td>
		  </tr>
		  <tr>
			<td colspan="3"><input name='gVEmp_rssfeed_url' type='text' id='gVEmp_rssfeed_url'  value='<?php echo $gVEmp_rssfeed_url; ?>' size="70" /></td>
		  </tr>
		  <tr>
			<td width="275">Title</td>
			<td width="10">&nbsp;</td>
			<td width="275">Number of Feeds</td>
		  </tr>
		  <tr>
			<td><input name='gVEmp_rssfeed_title' type='text' id='gVEmp_rssfeed_title'  value='<?php echo $gVEmp_rssfeed_title; ?>' size="30" maxlength="100" /></td>
			<td>&nbsp;</td>
			<td><input name='gVEmp_rssfeed_numberfeed' type='text' id='gVEmp_rssfeed_numberfeed'  value='<?php echo $gVEmp_rssfeed_numberfeed; ?>' size="30" maxlength="3" /></td>
		  </tr>
		  <tr>
			<td>No Announcement Text</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>
			<input type="hidden" id="gVEmp_rssfeed_submit" name="gVEmp_rssfeed_submit" value="1" /></td>
		  </tr>
		</table>
	 
	<?php
}

function gVEmp_rssfeed_widget_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('ITF RSS feed', 'ITF RSS feed', 'gVEmp_rssfeed_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('ITF RSS feed', array('ITF RSS feed', 'widgets'), 'gVEmp_rssfeed_control', 'width=550');
	} 
}

function gVEmp_rssfeed_deactivation() 
{
	delete_option('gVEmp_rssfeed_title');
	delete_option('gVEmp_rssfeed_numberfeed');
	delete_option('gVEmp_rssfeed_noannouncement');
	delete_option('gVEmp_rssfeed_url', $rss2_url);
	// No required
}

add_action("plugins_loaded", "gVEmp_rssfeed_widget_init");
register_activation_hook(__FILE__, 'gVEmp_rssfeed_install');
register_deactivation_hook(__FILE__, 'gVEmp_rssfeed_deactivation');
add_action('init', 'gVEmp_rssfeed_widget_init');
?>