<?php
/*
Plugin Name: Veuse Pagelist
Plugin URI: http://veuse.com/veuse-analytics
Description: Creates a post-type for featuredpages and two taxonomies. Fully localized. Templates included. This is an add-on for the Veuse Pagebuilder plugin.
Version: 1.0
Author: Andreas Wilthil
Author URI: http://veuse.com
License: GPL3
Text Domain: veuse-featuredpages
Domain Path: /languages
*/

__('Veuse Pagelist', 'veuse-pagelist' ); /* Dummy call for plugin name translation. */


class VeusePagelist{

	private $pluginURI = '';
	private $pluginPATH = '';
	
	function __construct(){
		
		$this->pluginURI  = plugin_dir_url(__FILE__) ;
		$this->pluginPATH = plugin_dir_path(__FILE__) ;
		
		add_action('wp_enqueue_scripts', array(&$this,'enqueue_styles'));
		add_action('wp_enqueue_scripts', array(&$this,'enqueue_scripts'),100);
		add_action('admin_enqueue_scripts', array(&$this,'admin_enqueue_scripts'),100);
		add_action('plugins_loaded', array(&$this,'load_textdomain'));
		
		add_shortcode('veuse_pagelist', array(&$this,'pagelist_shortcode'));
		
		add_action('media_buttons_context',  array(&$this,'add_my_custom_button'));
		add_action( 'admin_footer',  array(&$this,'add_inline_popup_content' ));
		/* Add support for excerpt on pages  */
		add_post_type_support('page', 'excerpt');
		
	}
	
	
	
	/* Enqueue scripts
	============================================= */
	
	function enqueue_styles() {
	
			wp_register_style( 'veuse-pagelist',  $this->pluginURI . 'assets/css/veuse-pagelist.css', array(), '', 'screen' );
			wp_enqueue_style ( 'veuse-pagelist' );
					
	}
	
	function enqueue_scripts() {
	
			wp_enqueue_script('veuse-pagelist-front', $this->pluginURI . 'assets/js/veuse-pagelist-front.js', array('jquery'), '', true);
	
	}
	
	function admin_enqueue_scripts() {
	
			wp_enqueue_script('veuse-pagelist', $this->pluginURI . 'assets/js/veuse-pagelist.js', array('jquery'), '', true);
	
	}
	
	/* Localization
	============================================= */
	
	function load_textdomain() {
	    load_plugin_textdomain('veuse-pagelist', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	
	/* Find template part

	Makes it possible to override the loop-files with
	files located in theme folder. Must be in a sub-directory named template-parts
	
	============================================ */
	
	function locate_part($file, $dir) {
			
			//$arg_list = func_get_args();
	
		     if ( file_exists( get_stylesheet_directory().'/'. $file .'.php')){
		     	$filepath = get_stylesheet_directory().'/'. $file .'.php';
		     }
		     elseif ( file_exists(get_template_directory().'/'. $file .'.php')){
		     	$filepath = get_template_directory().'/'. $file .'.php';
		     }
		     else {
		        $filepath = $this->pluginPATH . $dir.'/'.$file.'.php';
		       }
		    
		    return $filepath;
	}
	
	/* Shortcode
	============================================= */
	
	function pagelist_shortcode( $atts, $content = null ) {
	
			 extract(shortcode_atts(array(
						'id' 			=> '',
						'columns'		=> '3',
						'layout'		=> 'featured',
						'excerpt'		=> true,
						'title'			=> true,
						'link'			=> true,
						'image'			=> true,
						'imagesize'		=> null,
						'linktext'		=> __('Read more','veuse-pagelist'),
						'morelink'		=> false
	
	
			    ), $atts));
	
	
			$content = '';		
			
		
			$page_ids = explode(',',$id); // Explode to array
		
			foreach ($page_ids as $page_id):
	
				$featuredpage = get_page($page_id);
				$order = $featuredpage->menu_order;
				$featuredpages[] = array('id' => $featuredpage->ID, 'order' => $order);
			
			endforeach;
			
			$featuredpages = $this->veuse_pagelist_multisort( $featuredpages , array('order') );
			
			/*
			echo '<pre>';
			var_dump($featuredpages);
			echo '</pre>';
			*/
			
			if(isset($imagesize)){
				
				$imagesize = str_replace(' ','',$imagesize); // Remove whitespace
				$string_parts = explode("x", $imagesize); 
		
				$imagesize = array();
				$imagesize['width'] = $string_parts[0];
				$imagesize['height'] = $string_parts[1];
				
			}
			
			
			ob_start();
			
			switch ($layout) {
				
				case 'featured':
					include($this->locate_part( 'loop-pagelist-featured', 'loops' ));
					break;
				
				case 'highlights':
					
					include($this->locate_part( 'loop-pagelist-highlights', 'loops' ));
					break;
				
				case 'services':
					
					include($this->locate_part( 'loop-pagelist-services', 'loops' ));
					break;
				
				case 'tabs':
					
					include($this->locate_part( 'loop-pagelist-tabs', 'loops' ));
					break;
				
				case 'accordion':
					
					include($this->locate_part( 'loop-pagelist-tabs', 'loops' ));
					break;
				
				case 'auto':
					
					include($this->locate_part( 'loop-pagelist-tabs', 'loops' ));
					break;
				
			}
			
					
			$content.= ob_get_contents();
			
			ob_end_clean();
			
			
			return $content;
	
			
		}
		
		
		/* For sorting multidimensional array (page sorting by order)*/
		function veuse_pagelist_multisort($array, $sort_by) {
			 $evalstring = '';
			 foreach ($array as $key => $value) {
		        $evalstring = '';
		        foreach ($sort_by as $sort_field) {
			            $tmp[$sort_field][$key] = $value[$sort_field];
			            $evalstring .= '$tmp[\'' . $sort_field . '\'], ';
			        }
			    }
			    $evalstring .= '$array';
			    $evalstring = 'array_multisort(' . $evalstring . ');';
			    eval($evalstring);
				return $array;
		}
		
		function add_my_custom_button($context) {

			  //path to my icon
			  $img = $this->pluginURI.'assets/images/icon-pagelist.png';
			
			  //our popup's title
			  $title = 'Insert pagelist';
			
			  //append the icon
			  $context .= "<a href='#TB_inline?&width=640&height=600&inlineId=veuse-pagelist-popup&modal=false' class='thickbox' style='width:24px; margin:0; padding:0 !important;' title='{$title}'>
		    <img src='{$img}' width='24' height='24' style='margin:-1px 0 0 2px;'/></a>";
		    
			
			  return $context;
			}
			
		function add_inline_popup_content() {
			?>
			 <style>
			 	
			 	#TB_overlay { z-index: 9998 !important; }
			 	#TB_window { z-index: 9999 !important; }
			 	
			  	form#veuse-pagelist-insert { margin:0; width: auto; padding: 0; display: block;}
			  	form#veuse-pagelist-insert p { margin-bottom: 8px;}
			  	form#veuse-pagelist-insert hr { border:0; border-top:1px solid #eee !important; margin:20px 0; background-color: #eee !important;}
			  	form#veuse-pagelist-insert > section { margin-bottom: 10px; /*border-bottom: 1px dotted #d4d4d4;*/}
			  	form#veuse-pagelist-insert > section.half {width:50%; float: left;}
			  	form#veuse-pagelist-insert > section.third {width:33.33%; float: left;}
			  	form#veuse-pagelist-insert > section.twothird {width:66.66%; float: left;}
			  	.page-selector-item {
				  	
				  	padding:2px 6px; border: 1px solid #eee; display: inline-block; margin: 0 4px 4px 0; cursor: pointer;
			  	}
			  	
			  	.page-selector-item.active,
			  	.page-selector-item.active:hover {
				  	
				  	background: #2a95c5; border-color:#21759b; color:#fff !important;
			  	}
			  	
			  	ul#layout-selector,
			  	ul#column-selector { margin:0; } 
			  	
			  	ul#element-selector li,
			  	ul#layout-selector li,
			  	ul#column-selector li  { display: inline-block;  }	  	
			  	
			  	ul#element-selector li a,
			  	ul#layout-selector li a,
			  	ul#column-selector li a{ color:#21759b !important; display: inline-block; padding:2px 6px; border:1px solid #eee; text-decoration: none;}
			  	
			  	ul#element-selector li a.active,
			  	ul#layout-selector li a.active,
			  	ul#column-selector li a.active  {   	
				  	background: #2a95c5; border-color:#21759b; color:#fff !important;
			  	}
			  	
			  	ul#column-selector.disabled li a { pointer-events: none;}
			  	
			
			  
			  </style>
			<div id="veuse-pagelist-popup" style="width:100%; height:100%; display:none;">
			  <h2>Insert pagelist: This lets you insert pages in a grid layout.</h2>
			 
			  <script>
			  
			  	jQuery(function($){
			  	
			  		
			  		jQuery('a.page-selector-item').click(function(){
			  			$(this).toggleClass('active');
			  			return false;
			  		});
		
					jQuery('a.element-selector-item').click(function(){
			  			$(this).toggleClass('active');
			  			return false;
			  		});
		
						  		
			  		jQuery('#layout-selector a').click(function(){
			  		
			  			$('#layout-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			
			  			var selectedVal = $(this,'.active').attr('data-id');
			  			
			  			if(selectedVal == 'accordion' || selectedVal == 'tabs' || selectedVal == 'auto' ){
				  			jQuery('#column-selector').fadeTo('300',0.2).addClass('disabled');
			  			} else {
				  			jQuery('#column-selector').fadeTo('300',1).removeClass('disabled');;
			  			}
			  				  			
			  			return false;
			  		});
			  		
			  		jQuery('#column-selector a').click(function(){
			  			$('#column-selector a').removeClass('active');
			  			$(this).addClass('active');
			  			return false;
			  		});
			  		
			  	
			  		 	  		
				  	jQuery('#insert-pagelist-shortcode').click(function(){
					  	
					  	var shortcodeText;
					
					  	var ids = '';
					  	
						$('#page-selector a.active').each(function(){
							
							ids += $(this).attr('data-id') + ',';
						});
						
						ids = ids.substring(0, ids.length-1);
						
						var layout = $('#layout-selector a.active').attr('data-id');
						var columns = $('#column-selector a.active').attr('data-id');
						
						var displayexcerpt;
						if ($('#element-selector').find('a[data-id=excerpt]').hasClass('active')){
							displayexcerpt = 'true';
						} else {
							displayexcerpt = 'false';
						}
						
						var displaytitle;
						if ($('#element-selector').find('a[data-id=title]').hasClass('active')){
							displaytitle = 'true';
						} else {
							displaytitle = 'false';
						}
						
						var displayimage;
						if ($('#element-selector').find('a[data-id=image]').hasClass('active')){
							displayimage = 'true';
						} else {
							displayimage = 'false';
						}
						
						var displaymorelink;
						if ($('#element-selector').find('a[data-id=morelink]').hasClass('active')){
							displaymorelink = 'true';
						} else {
							displaymorelink = 'false';
						}
						
						var link = document.getElementById('insert-link');	
						
						if(link.checked){
							insertlink = 'true';
						}
						else {
							insertlink = 'false';
							}
										
								  		
					  	shortcodeText = '[veuse_pagelist  id="' + ids + '" layout="' + layout + '" columns="'+ columns +'" title="' + displaytitle + '" excerpt="' + displayexcerpt +'" image="' + displayimage +'" morelink="' + displaymorelink +'" link="' + insertlink + '"]';
					  	 tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcodeText);
					  	 tb_remove();
					  	 return false;
				  	});
				  	
			  	});
			  
			  
			  
			  </script>
			  
			  
			  <form id="veuse-pagelist-insert">
			  
			  	<section class="twothird">
				<p><strong><?php _e('Layout','veuse-pagelist');?></strong></p>
				
					<ul id="layout-selector">
								
						<li><a href="#" class="layout-selector-item active" data-id="featured">Featured pages</a></li>
						<li><a href="#" class="layout-selector-item" data-id="highlights">Highlights</a></li>
						<li><a href="#" class="layout-selector-item" data-id="tabs">Tabs</a></li>
						<li><a href="#" class="layout-selector-item" data-id="accordion">Accordion</a></li>
						<li><a href="#" class="layout-selector-item" data-id="auto">Auto</a></li>
								
					</ul>
				</section>	
				
				<section id="column-selector-wrapper" class="third">
				<p><strong><?php _e('Columns','veuse-pagelist');?></strong></p>
					
					<ul id="column-selector">
								
						<li><a href="#" class="column-selector-item" data-id="1">1</a></li>
						<li><a href="#" class="column-selector-item" data-id="2">2</a></li>
						<li><a href="#" class="column-selector-item active" data-id="3">3</a></li>
						<li><a href="#" class="column-selector-item" data-id="4">4</a></li>
															
					</ul>
				</section>
				
			  	 <section>
					<p><strong><?php _e('Pages','veuse-pagelist');?></strong></p>
					 
					 <div id="page-selector">
									<?php 
										$pages = get_pages('orderby=title');
										foreach($pages as $page){
											echo '<a data-id="'.$page->ID.'" class="page-selector-item">'.$page->post_title.'</a>';
										}
									?>
					</div>
			  	</section>
			  	
			  	 <section>
					<p><strong><?php _e('Elements','veuse-pagelist');?></strong></p>
					 
					<ul id="element-selector">
								
						<li><a href="#" class="element-selector-item active" data-id="title">Title</a></li>
						<li><a href="#" class="element-selector-item active" data-id="excerpt">Excerpt</a></li>
						<li><a href="#" class="element-selector-item active" data-id="image">Thumbnail</a></li>
						<li><a href="#" class="element-selector-item" data-id="morelink">Read-more link</a></li>
															
					</ul>
			  	</section>
				
				
				<section>
				<p><strong><?php _e('Image size','veuse-pagelist');?></strong></p>
				<p class="description"> - To override the predefined image sizes. Format the string with height-value x width-value, ie. 400x200</p>
					
					<input type="text" name="image-size" id="image-size" /><label for="image-size"> Enter image size</label>
				</section>
				
				<section>
				<p><strong><?php _e('Link','veuse-pagelist');?></strong></p>
							
					<input type="checkbox" name="insert-link" id="insert-link" checked="checked" /><label for="insert-link"> Link item to page</label>
				</section>
				<hr>		
				<input type="submit" class="button-primary" id="insert-pagelist-shortcode"  value="<?php _e('Insert shortcode') ?>" />	  
			  </form>
			</div>
			<?php
			}


	
}


$pagelist = new VeusePagelist;


/* Widget */
require_once(plugin_dir_path(__FILE__). 'widget.php');

/* Creates page excerpts for use in loops etc.
====================================================== */
if(!function_exists('veuse_pagelist_excerpt')){
	
	function veuse_pagelist_excerpt($args) {

	  global $post;

	  extract($args);

	  if( $page_id == null ) return false;

	  $page_data 	= get_page( $page_id );
	  $page_excerpt = $page_data->post_excerpt;
	  $page_content = $page_data->post_content;

	  if(!empty($page_content)){
	  		$limit = strpos( $page_content, "<!--more-->", 1);
	  		$val =  substr($page_content, 0, $limit);
	  	}


	  $output = '';

	  // If page has excerpt...

	  if(!empty($page_excerpt)){

	  	
		$output .= apply_filters('the_excerpt',$page_excerpt);
	  	


	  	return $output;
	  }


	  // If page has more-quicktag, echo up to <!--more-->

	  elseif(!empty($limit)){

	 	$output .= wpautop(do_shortcode($val));
	  

	  	return $output;
	    }

	  // Else echo the content in full

	  else {

	  	return wpautop(do_shortcode($page_content));

	  }
	}
	
	
	
	
	
	
}


/* Plugin options */

// ------------------------------------------------------------------------
// PLUGIN PREFIX:
// ------------------------------------------------------------------------
// A PREFIX IS USED TO AVOID CONFLICTS WITH EXISTING PLUGIN FUNCTION NAMES.
// WHEN CREATING A NEW PLUGIN, CHANGE THE PREFIX AND USE YOUR TEXT EDITORS
// SEARCH/REPLACE FUNCTION TO RENAME THEM ALL QUICKLY.
// ------------------------------------------------------------------------

// 'veuse_pagelist_' prefix is derived from [p]plugin [o]ptions [s]tarter [k]it

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// HOOKS TO SETUP DEFAULT PLUGIN OPTIONS, HANDLE CLEAN-UP OF OPTIONS WHEN
// PLUGIN IS DEACTIVATED AND DELETED, INITIALISE PLUGIN, ADD OPTIONS PAGE.
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'veuse_pagelist_add_defaults');
register_uninstall_hook(__FILE__, 'veuse_pagelist_delete_plugin_options');
add_action('admin_init', 'veuse_pagelist_init' );
add_action('admin_menu', 'veuse_pagelist_add_options_page');
add_filter( 'plugin_action_links', 'veuse_pagelist_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'veuse_pagelist_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function veuse_pagelist_delete_plugin_options() {
	delete_option('veuse_pagelist_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'veuse_pagelist_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function veuse_pagelist_add_defaults() {
	$tmp = get_option('veuse_pagelist_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('veuse_pagelist_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"css" => "1");
		update_option('veuse_pagelist_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'veuse_pagelist_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function veuse_pagelist_init(){
	register_setting( 'veuse_pagelist_plugin_options', 'veuse_pagelist_options', 'veuse_pagelist_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'veuse_pagelist_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function veuse_pagelist_add_options_page() {
	add_options_page('Veuse Pagelist Options Page', 'Pagelist', 'manage_options', __FILE__, 'veuse_pagelist_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function veuse_pagelist_render_form() {
	?>
	<div class="wrap">

		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e('Featured Pages settings','veuse-pagelist');?></h2>
		<p><?php _e('Settings for the featured-pages plugin.','veuse-pagelist');?></p>

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('veuse_pagelist_plugin_options'); ?>
			<?php $options = get_option('veuse_pagelist_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<table class="form-table">


				<tr>
					<th scope="row"><strong><?php _e('Enable plugin stylesheet','veuse-pagelist');?></strong></th>
					<td>
						<input name="veuse_pagelist_options[css]" type="checkbox" <?php if(isset($options['css'])) echo 'checked="checked"'; ?>/>
					</td>
				</tr>


			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>


	</div>
	<?php
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function veuse_pagelist_validate_options($input) {
	 // strip html from textboxes
	//$input['css'] =  	$options['css']; // Sanitize textarea input (strip html tags, and escape characters)
	//$input['lightbox'] =$options['lightbox']; // Sanitize textarea input (strip html tags, and escape characters)

	//$input['txt_one'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function veuse_pagelist_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$veuse_pagelist_links = '<a href="'.get_admin_url().'options-general.php?page=veuse-featured-pages/veuse-featured-pages.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $veuse_pagelist_links );
	}

	return $links;
}


/* Image resizer */

/**************************************
** GET IMAGE ID FROM SRC **
***************************************/
if(!function_exists('ceon_image_src')){

	function ceon_image_src($post_id,$size){

		$url =  wp_get_attachment_image_src ( get_post_thumbnail_id ( $post_id ),'full');
		return $url[0];

	}
}



/* Insert retina image */

if(!function_exists('veuse_retina_interchange_image')){

	function veuse_retina_interchange_image($img_url, $width, $height, $crop){

		$imagepath = '<img src="'. mr_image_resize($img_url, $width, $height, $crop, 'c', false) .'" data-interchange="['. mr_image_resize($img_url, $width, $height, $crop, 'c', true) .', (retina)]" alt=""/>';
	
		return $imagepath;
	
	}
}
?>