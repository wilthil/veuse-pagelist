<?php 

         
 

/* Loop for displaying featured pages with layout "Highlight" */

$content .= ' <div class="veuse-pagelist-tabs"><ul class="resp-tabs-list">';
$i = 0;
foreach ($featuredpages as $page){
			
	$page_id = $page['id'];
	$page = get_page($page_id);
	
	$content .= '<li>';
	$content .= '' . $page->post_title .'';
	$content .= '</li>';
	$i++;
		
}
$content .= '</ul>';

$content .= '<div class="resp-tabs-container">';

$i = 0;
foreach ($featuredpages as $page){
			
	$page_id = $page['id'];
	$page = get_page($page_id);
	


	$content .= '<div>';
    $content .= '<h3>' . $page->post_title .'</h3>';
	$excerpt_args = array('page_id'	=> $page_id);

	$content .= veuse_pagelist_excerpt($excerpt_args);
	
	
	if($morelink == 'true' ){
	$content .= '<a href="'. get_permalink($page_id) .'" title="">'. $linktext.'</a>';
	}

	$content .= '</div>';
	$i++;	
}
$content .= '</div></div>';
	
?>