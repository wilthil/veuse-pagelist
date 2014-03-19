<?php 

/* Image sizes */

$ratio = 0.5;

if(!isset($imagesize)){
	if($columns == '1') { $imagesize = array( 'width' => 1000, 'height' => 1000 * $ratio ); }
	if($columns == '2') { $imagesize = array( 'width' => 1000, 'height' => 1000 * $ratio ); }
	if($columns == '3') { $imagesize = array( 'width' => 666, 'height' => 666 * $ratio ); }
	if($columns == '4') { $imagesize = array( 'width' => 500, 'height' => 500 * $ratio ); }
}


/* Loop for displaying featured pages with layout "Featured" */

$content .= '<ul class="small-pagelist-block-grid-2 large-pagelist-block-grid-'. $columns .'">';

foreach ($featuredpages as $page){
	
	$page_id = $page['id'];	
	$page = get_page($page_id);
	

	
	$content .= '<li><div class="post type-'.$layout.'">';
	if( $image == 'true' && has_post_thumbnail($page_id)) {
		$content .= '<div class="entry-thumbnail">';
		if($link == true){
			$content .= '<a href="'. get_permalink($page_id) .'" title="">';
		}
		$img_url = wp_get_attachment_url( get_post_thumbnail_id($page_id) );
		$content .=  veuse_retina_interchange_image( $img_url, $imagesize['width'], $imagesize['height'], true);
		
		if($link == true){
			$content .= '</a>';
		}
		
		$content .= '</div>';
	}
			
	if($title == 'true'){ 
		
		if($link == true){
			$content .= '<a href="'. get_permalink($page_id) .'" title="">';
		}
		$content .= '<h4>' .$page->post_title .'</h4>';
		if($link == true){
			$content .= '</a>';
		}
	}
	if($excerpt == 'true'){ 
		$content .= '<p>' .$page->post_excerpt .'</p>';
	}
	if($morelink == true ){
	$content .= '<a href="'. get_permalink($page_id) .'" title="">'. $linktext.'</a>';
	}
	$content .= '</div></li>';		
}
$content .= '</ul>';
	
?>