<?php
/*
Plugin Name: usercloud24 
Plugin URI: http://leumund.ch/usercloud24
Description: Shows a Cloud with the Names and the URL of the Users from the last 24 hours. Zeigt die Besucher der letzten 24 Stunden als Benutzerwolke an.
Version: 2.0
License: GPL
Author: Christian Leu
Author URI: http://leumund.ch
*/

/*  Copyright 2009  usercloud24  (email : me@relab.ch)

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


$usercloud24_db_version = "1.1";

function usercloud24_install () {
 global $wpdb;
 global $usercloud24_db_version;


   $table_name = $wpdb->prefix . "usercloud24";
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {

$sql = "CREATE TABLE " . $table_name . "(id mediumint(9) NOT NULL AUTO_INCREMENT,time timestamp NOT NULL default CURRENT_TIMESTAMP,name tinytext NOT NULL,url VARCHAR(200) NOT NULL, UNIQUE KEY id (id));";



require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);





      add_option("usercloud24_db_version", $usercloud24_db_version);
      
      
      
  $installed_ver = get_option( "usercloud24_db_version" );

   if( $installed_ver != $usercloud24_db_version ) {

  $sql = "CREATE TABLE " . $table_name . "(id mediumint(9) NOT NULL AUTO_INCREMENT,time timestamp NOT NULL default CURRENT_TIMESTAMP,name tinytext NOT NULL,url VARCHAR(200) NOT NULL, UNIQUE KEY id (id));";


      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      update_option( "usercloud24_db_version", $usercloud24_db_version );
  }
    
      
      
  $insertfirst = "INSERT INTO `leumundc_reader`.`b2bwp_usercloud24` (
`id` ,
`time` ,
`name` ,
`url`
)
VALUES (
NULL ,
CURRENT_TIMESTAMP , 'usercloud24', 'http://leumund.ch/usercloud24'
);";
$wpdb->query($insertfirst);      
      
      
      
}
}
 


 function usercloud24_writeUser() {
        global $wpdb, $wp_query, $statz_options, $isBot, $isAdmin, $user_url, $user_ID, $user_identity, $user_level;
        
     $useragent = $wpdb->escape($_SERVER["HTTP_USER_AGENT"]);
       $isBot = usercloud24_AreYouBot($useragent);
     
		if($user_ID > 0) {
		    $useronline = $user_identity;
            $userurl = $user_url;
            if($user_level >= 0 && $user_level < 8) {
                $isAdmin = 0;
            }
            elseif($user_level >= 8 && $user_level <= 10) { 
                $isAdmin = 1;
                
            }
        }
        elseif(isset($_COOKIE["comment_author_".COOKIEHASH])) {
            $useronline = trim($_COOKIE["comment_author_".COOKIEHASH]);
            $userurl = trim($_COOKIE["comment_author_url_".COOKIEHASH]);
            $useremail = trim($_COOKIE["comment_author_email_".COOKIEHASH]);

            $isAdmin = -1;
            
           $ok_to_comment = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_author = '$useronline' and comment_author_email = '$useremail' and comment_approved = '1' LIMIT 1");
          
            if  ( 1 != $ok_to_comment ) {
              
             $isbot = true; 
              }
          
           
        }
        else {
            $useronline = "Gast"; $isAdmin = -1;
        }
     $username = $useronline;
     
   //  echo $isAdmin;
     
   //  if ($username!="Gast" and $isAdmin != "1") {  
      
       if ($username!="Gast" and $isbot == false and $isAdmin != "1")  {  
 
 
      $sql ="Insert into ".$wpdb->prefix ."usercloud24(name,url) VALUES ('$username', '$userurl')";
        $wpdb->query($sql);  
     //   echo $sql;
        
 
    }
    }
    
 
  add_action("wp_head", "usercloud24_writeUser"); // Aktion hinzufügen damit gelogt wird. 




function get_usercloud24($showdays = '1', $style='cloud', $title='Usercloud24', $min='8',$max ='25', $unit='pt') {
global $table_prefix, $wpdb, $wp_query;

//Alle Einträge älter wie 100 Tage lšschen
// Test für phpMyAdmin: select * FROM wp_usercloud24 WHERE time < date_sub(current_date(), interval 24 hour)
$sql = "DELETE FROM ".$wpdb->prefix ."usercloud24 WHERE time < DATE_SUB(CURDATE(),INTERVAL 100 DAY)";
$wpdb->query($sql);  

if (($showdays>="1") and ($showdays<="100")) {   } else { $showdays="1"; }

//echo $showdays;
// Einträge nach User gruppiert zählen. 
$query = "SELECT count(name) as num, name as username, url as comment_author_url from ".$wpdb->prefix ."usercloud24 WHERE time > DATE_SUB(CURDATE(),INTERVAL ".$showdays." DAY) group by name";
$result = mysql_query($query);



			$num_rows = mysql_num_rows($result);
			If($num_rows == 0) {
//if ($result) { 
echo "<a href=\"http://leumund.ch/usercloud24\">Usercloud24 installed! Waiting for Visitors</a>";  
} 

else {

// here we loop through the results and put them into a simple array:
// $tag['thing1'] = 12;
// $tag['thing2'] = 25;
// etc. so we can use all the nifty array functions
// to calculate the font-size of each tag
while ($row = mysql_fetch_array($result)) {
    $tags[$row['username']] = $row['num'];
    $category_id[$row['username']] = $row['comment_author_url'];
}

//$return = wp_generate_tag_cloud( $tags, $args );





// change these font sizes if you will


	
	
if ( 'cloud' == $style ) {
	$max_size = $max; // max font size in px
$min_size = $min; // min font size in px

// get the largest and smallest array values
$max_qty = max(array_values($tags));
$min_qty = min(array_values($tags));

// find the range of values
$spread = $max_qty - $min_qty;
if (0 == $spread) { // we don't want to divide by zero
    $spread = 1;
}

// determine the font-size increment
// this is the increase per tag quantity (times used)
$step = ($max_size - $min_size)/($spread);

// loop through our tag array
	
foreach ($tags as $key => $value) {


    // calculate CSS font-size
    // find the $value in excess of $min_qty
    // multiply by the font-size increment ($size)
    // and add the $min_size set above
    $size = $min_size + (($value - $min_qty) * $step);
    // uncomment if you want sizes in whole %:
     $size = round($size,0);
		
	
	
if ($category_id[$key]) {
    // you'll need to put the link destination in place of the #
    //  (assuming your tag links to some sort of details page)
        echo '<a href="'.$category_id[$key].'" class="usercloud" target="_blank" style="font-size: '.$size.'pt"';
    // perhaps adjust this title attribute for the things that are tagged
    echo ' title="'.$value.' seitenaufrufe  durch '.$key.'"';
    echo '>'.$key.'</a>';
    echo "\n";
    
   }
   
   else {
       // you'll need to put the link destination in place of the #
    //  (assuming your tag links to some sort of details page)
        echo '<span class="usercloud" style="font-size: '.$size.'pt"';
    // perhaps adjust this title attribute for the things that are tagged
 //   echo ' title="'.$value.' seitenaufrufe  durch '.$key.'"';
    echo '>'.$key.'</span>';
    echo "\n";
   } 
    }
   }
   elseif ( 'list' == $style ) {
if ($title) {   echo '<li class="usercloud24">' . $title ; }
echo  '<ul>';
   foreach ($tags as $key => $value) {
if ($category_id[$key]) {
   			echo '<li><a href="' .  $category_id[$key]  . '">' . $key . '</a></li>'; 
   			} else {    	//		echo '<li>'. $key . '</li>'; 
   			}

   		}
   		echo '</li></ul>';
		}
	
	/*
 elseif ( 'special' == $style )  {
    	foreach ($tags as $key => $value) {

    	echo '<li><a href="' .  $category_id[$key]  . '">' . $key . '</a></li>'; 
}
 }
   */
   
    // notice the space at the end of the link


 }

    
} # end usercloud function

 function usercloud24_AreYouBot($user_agent) {
        global $isBot;
        $bots = array(
            'aipbot',
            'amfibibot',
            'appie',
            'ask jeeves/teoma',
            'aspseek',
            'axadine',
            'baiduspider',
            'becomebot',
            'blogcorpuscrawler',
            'blogpulse',
            'blogsnowbot',
            'boitho.com',
            'bruinbot',
            'cerberian',
            'cfnetwork',
            'check_http',
            'cipinetbot',
            'claymont',
            'cometsearch@cometsystems.com',
            'converacrawler',
            'cydralspider',
            'digger',
            'es.net_crawler',
            'eventax',
            'everyfeed-spider',
            'exabot@exava.com',
            'faxobot',
            'findlinks',
            'fireball',
            'francis',
            'gaisbot',
            'gamekitbot',
            'gazz@nttr.co.jp',
            'geonabot',
            'getrax crawler',
            'gigabot',
            'girafa.com',
            'goforitbot',
            'googlebot',
            'grub-client',
            'holmes',
            'houxoucrawler',
            'http://www.almaden.ibm.com/cs/crawler',
            'http://www.istarthere.com',
            'http://www.relevantnoise.com',
            'httrack ?',
            'ia_archiver',
            'ichiro',
            'iltrovatore-setaccio',
            'inelabot',
            'infoseek',
            'inktomi.com',
            'irlbot',
            'jetbot',
            'jobspider_ba',
            'kazoombot',
            'larbin',
            'libwww',
            'linkwalker',
            'lmspider',
            'mackster',
            'mediapartners-google',
            'microsoft url control',
            'mj12bot',
            'moreoverbot',
            'mozdex',
            'msnbot',
            'msrbot',
            'naverbot',
            'netresearchserver',
            'ng/2.0',
            'np(bot)',
            'nutch',
            'objectssearch',
            'ocelli',
            'omniexplorer_bot',
            'openbot',
            'overture',
            'patwebbot',
            'php',
            'phpdig',
            'pilgrim html-crawler',
            'pipeliner',
            'pompos',
            'psbot',
            'python-urllib',
            'quepasacreep',
            'robozilla',
            'rpt-httpclient',
            'savvybot',
            'scooter',
            'search.ch',
            'seekbot',
            'semager',
            'seznambot',
            'sherlock',
            'shelob',
            'sitesearch',
            'snapbot',
            'snappreviewbot',
            'speedy spider',
            'sphere scout',
            'stackrambler',
            'steeler',
            'surveybot',
            'szukacz',
            'technoratibot',
            'telnet',
            'themiragorobot',
            'thesubot',
            'thumbshots-de-bot',
            'topicblogs',
            'turnitinbot',
            'tutorgigbot',
            'tutorial crawler',
            'vagabondo',
            'versus',
            'voilabot',
            'w3c_css_validator',
            'w3c_validator',
            'w3c-checklink',
            'web downloader',
            'webcopier',
            'webcrawler',
            'webfilter robot',
            'west wind internet protocols',
            'wget',
            'wwweasel robot',
            'wwwster',
            'xaldon webspider',
            'xenu',
            'yahoo! slurp',
            'yahoofeedseeker',
            'yahoo-mmcrawler',
            'zao',
            'zipppbot',
            'zyborg',
        );
        foreach($bots as $bot) { 
            if(stristr($user_agent, $bot) !== false) {
                return true;
            }
        }
        return false;
    }

function widget_usercloud24_init() {
	if (!function_exists('register_sidebar_widget')) return;

	function widget_usercloud24($args) {
		
		extract($args);

		$options = get_option('widget_usercloud24');
		$title = $options['title'];
		$showdays = $options['showdays'];
		$style= "list";
		
if ( empty($title) ) {
			$title = 'usercloud24';
		} 
		
		$options['title']  = empty($options['title']) ? 'usercloud24' : $options['title'] ;
		$options['showdays'] = empty($options['showdays']) ? '10' : $options['showdays'];
		$options['style'] = empty($options['style']) ? 'cloud' : $options['style'];		
		$options['smin'] = empty($options['smin']) ? '8' : $options['smin'];		
		$options['smax'] = empty($options['smax']) ? '22' : $options['smax'];
		$options['unit'] = empty($options['unit']) ? 'pt' : $options['unit'];	
		
//echo "testecho";
	//	echo $showdays;
		echo $before_widget;
		echo $before_title . $title . $after_title;
		get_usercloud24($showdays,$options['style'],'',$options['smin'],$options['smax'],$options['unit']);
		echo $after_widget;

	}

	function widget_usercloud24_control() {
		$options = get_option('widget_usercloud24');
		if ( !is_array($options) ) {
			$options = array('title'=>'usercloud24');
			$options = array('showdays'=>'');
			$options = array('style'=>'');
			
			
			

}
		if ( $_POST['usercloud24-submit'] ) {
		if (($_POST['usercloud24-showdays']>="1") and ($_POST['usercloud24-showdays']<="100")) {   } else { $_POST['usercloud24-showdays']="1"; }

			$options['title'] = strip_tags(stripslashes($_POST['widget-usercloud24-title']));
			$options['showdays'] = strip_tags(stripslashes($_POST['widget-usercloud24-showdays']));
			$options['style'] = strip_tags(stripslashes($_POST['widget-usercloud24-style']));
			$options['smin'] = strip_tags(stripslashes($_POST['widget-usercloud24-smin']));
			$options['smax'] = strip_tags(stripslashes($_POST['widget-usercloud24-smax']));
			$options['unit'] = strip_tags(stripslashes($_POST['widget-usercloud24-unit']));



			update_option('widget_usercloud24', $options);
		}
		
$options['title']  = empty($options['title']) ? 'usercloud24' : $options['title'] ;
		$options['showdays'] = empty($options['showdays']) ? '10' : $options['showdays'];
		$options['smin'] = empty($options['smin']) ? '8' : $options['smin'];		
		$options['smax'] = empty($options['smax']) ? '22' : $options['smax'];
		$options['unit'] = empty($options['unit']) ? 'pt' : $options['unit'];	

		$title = htmlspecialchars($options['title'], ENT_QUOTES);
	//echo $title;
				$showdays = $options['showdays'];	

		
?>

<label for="widget-usercloud24-title" style="line-height:35px;display:block;">
				<?php _e('Title:', 'usercloud24'); ?>
				<input size="30"  type="text" id="widget-usercloud24-title" name="widget-usercloud24-title" value="<?php echo $options['title']; ?>" />
			</label>
<label for="widget-usercloud24-showdays" style="line-height:35px;display:block;">
				<?php _e('Show X Days (1-100):', 'usercloud24'); ?>
				<input size="30"  type="text" id="widget-usercloud24-showdays" name="widget-usercloud24-showdays" value="<?php echo $options['showdays']; ?>" />
			</label>
	<label for="widget-stags-style" style="line-height:35px;display:block;">
				<?php _e('Format:', 'usercloud24'); ?>
				<select id="widget-usercloud24-<?php echo $number; ?>" name="widget-usercloud24-style">
					<option <?php if ( $options['style'] == 'cloud' ) echo 'selected="selected"'; ?> value="cloud"><?php _e('Cloud (default)', 'usercloud24'); ?></option>
					<option <?php if ( $options['style'] == 'list' ) echo 'selected="selected"'; ?> value="list"><?php _e('List', 'usercloud24'); ?></option>
				</select>
			</label>
<p>Use this settings only when style is cloud</p>
	<label for="widget-usercloud24-unit" style="line-height:35px;display:block;">
				<?php _e('Unit font size:', 'usercloud24'); ?>
				<select id="widget-usercloud24-unit" name="widget-usercloud24-unit">
					<option <?php if ( $options['unit'] == 'pt' ) echo 'selected="selected"'; ?> value="pt"><?php _e('Point (default)', 'usercloud24'); ?></option>
					<option <?php if ( $options['unit'] == 'px' ) echo 'selected="selected"'; ?> value="px"><?php _e('Pixel', 'usercloud24'); ?></option>
					<option <?php if ( $options['unit'] == 'em' ) echo 'selected="selected"'; ?> value="em"><?php _e('Em', 'usercloud24'); ?></option>
					<option <?php if ( $options['unit'] == '%' ) echo 'selected="selected"'; ?> value="%"><?php _e('Pourcent', 'usercloud24'); ?></option>
				</select>
			</label>

<label for="widget-usercloud24-smini" style="line-height:35px;display:block;">
				<?php _e('Font size mini: (default: 8)', 'usercloud24'); ?>
				<input size="20"  type="text" id="widget-usercloud24-smin" name="widget-usercloud24-smin" value="<?php echo $options['smin']; ?>" />
			</label>

			<label for="widget-usercloud24-smax" style="line-height:35px;display:block;">
				<?php _e('Font size max: (default: 22)', 'simpletags'); ?>
				<input size="20" type="text" id="widget-usercloud24-smax" name="widget-usercloud24-smax" value="<?php echo $options['smax']; ?>" />
			</label>
	
	<?
	echo '<input type="hidden" id="usercloud24-submit" name="usercloud24-submit" value="1" />';
	}		

	register_sidebar_widget('usercloud24', 'widget_usercloud24');
	register_widget_control('usercloud24', 'widget_usercloud24_control', 300, 100);
}
register_activation_hook(__FILE__,'usercloud24_install');
add_action('plugins_loaded', 'widget_usercloud24_init');

?>