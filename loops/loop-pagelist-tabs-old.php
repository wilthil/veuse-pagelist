<?php 

/* Loop for displaying featured pages with layout "Highlight" */

$content .= '<div class="veuse-section-container '. $layout .'" data-section="'. $layout .'">';

foreach ($featuredpages as $page){
			
	$page_id = $page['id'];
	$page = get_page($page_id);
	
	$content .= '<section class="section">';
				
	$content .= '<p class="title"><a href="#">'. $page->post_title .'</a></p>';
	$content .= '<div class="content">';
	$content .= '<h4>' . $page->post_title .'</h4>';
	
	$excerpt_args = array('page_id'	=> $page_id);

	$content .= veuse_pagelist_excerpt($excerpt_args);
	
	
	if($morelink == 'true' ){
	$content .= '<a href="'. get_permalink($page_id) .'" title="">'. $linktext.'</a>';
	}

	$content .= '</div>';
	
	
	$content .= ' </section>';		
}
$content .= '</div>';
	
?>