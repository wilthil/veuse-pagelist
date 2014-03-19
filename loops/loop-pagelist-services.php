<?php 

/* Loop for displaying featured pages with layout "Highlight" */

$content .= '<ul class="small-block-grid-2 large-block-grid-'. $columns .'">';

foreach ($featuredpages as $page){
			
	$page_id = $page['id'];
	$page = get_page($page_id);
	
	$content .= '<li><div class="post type-'.$layout.'">';
	if(($image == 'true' || $image == 'yes') && has_post_thumbnail($page_id)) {
		
		$content .= '<div class="entry-thumbnail">' . get_the_post_thumbnail($page_id,'medium') . '</div>';
	}
			
	if($title == 'true'){ 
		$content .= '<h4>' .$page->post_title .'</h4>';
	}
	if($excerpt == 'true'){ 
		$content .= '<p>' .$page->post_excerpt .'</p>';
	}
	if($link == 'true' || $link == 'yes'){
	$content .= '<a href="'. get_permalink($page_id) .'" title="">'. $linktext.'</a>';
	}
	$content .= '</div></li>';		
}
$content .= '</ul>';
	
?>