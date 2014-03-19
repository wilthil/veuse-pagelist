<?php

class VeusePagelistWidget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'veuse_pagelist_widget', // Base ID
			__('Pagelist (Veuse)','veuse-pagelist'), // Name
			array( 'description' => __( 'Extract content from other pages and put in a grid on your page or post.', 'veuse-pagelist' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$grid = $instance['grid'];
		$veuse_pagelist_selected_pages = $instance['veuse_pagelist_selected_pages'];
		$imagesize = $instance['imagesize'];
		$layout = $instance['layout'];
		$link = $instance['link'];
		
		
		if(!empty($imagesize)) {
			
			$imagesize = 'imagesize="'.$imagesize.'"';
		}
	
		
		$veuse_pagelist_selected_pages = rtrim($veuse_pagelist_selected_pages, ',');


		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
			 // Do Your Widgety Stuff Here…
			 echo do_shortcode('[veuse_pagelist  id="'. $veuse_pagelist_selected_pages .'" columns="' . $grid . '" layout="' . $layout . '" link="'.$link.'" '.$imagesize.']');
		
		echo $after_widget;
	}


	public function update( $new_instance, $old_instance ) {
		
		$instance = array();
				
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['veuse_pagelist_selected_pages'] = strip_tags( $new_instance['veuse_pagelist_selected_pages'] );
		$instance['imagesize'] = strip_tags( $new_instance['imagesize'] );
		$instance['grid'] = strip_tags( $new_instance['grid'] );
		$instance['link'] = isset($new_instance['link']);
		$instance['layout'] = strip_tags( $new_instance['layout'] );
		
		return $instance;
	}

	 
	public function form( $instance ) {
	
		global $widget, $wp_widget_factory, $wp_query;
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( '', 'text_domain' );
		}
		
		if ( isset( $instance[ 'imagesize' ] ) ) {
			$imagesize = $instance[ 'imagesize' ];
		}
		else {
			$imagesize = '';
		}
		
		if ( isset( $instance[ 'veuse_pagelist_selected_pages' ] ) ) {
			$veuse_pagelist_selected_pages = $instance[ 'veuse_pagelist_selected_pages' ];
			
		}
		else {
			$veuse_pagelist_selected_pages = '';
		}
		
		if ( isset( $instance[ 'link' ] ) ) {
			$link = $instance[ 'link' ];
		}
		else {
			$link = true;
		}
	
		
		
		if ( isset( $instance[ 'grid' ] ) ) {
			$grid = $instance[ 'grid' ];
		}
		else {
			$grid = __( '3', 'text_domain' );
		}
		
		if ( isset( $instance[ 'layout' ] ) ) {
			$layout = $instance[ 'layout' ];
		}
		else {
			$layout = __( 'featured', 'text_domain' );
		}
		
			?>
		

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<style>
			.pageselector-wrapper {
				
				padding:10px; background: #fff; border:1px solid #eee; overflow: scroll; max-height:180px;
				
			}
			
			.pageselector-wrapper a { 
				padding:3px 10px 3px 0px;  display: block; margin: 0; cursor: pointer; text-decoration: none;
				border-bottom:1px dotted #d4d4d4;
			}
			
			.pageselector-wrapper a:hover { color:#2a95c5;}
						
			.pageselector-wrapper a:after {
					content:'';
					color:#999;
					float:right;
					font-weight: bold;
				} 
			.pageselector-wrapper a.active { font-weight: bold; color:#de4b29;}
			.pageselector-wrapper a.active:after {
					content:'x';
					color:#de4b29;
				
				} 
		</style>
		
		<label for="<?php echo $this->get_field_id( 'veuse_pagelist_selected_pages' ); ?>"><?php _e( "Select pages:",'veuse-pagelist' ); ?></label> 
		<div class="pageselector-wrapper" style="margin-bottom:20px;">
		<?php
		
		$veuse_pagelist_selected_pages_array = explode(',', $veuse_pagelist_selected_pages);
		
		$get_pages = get_pages( array( 
            'orderby' => 'orderby=title', 
            'order' => 'DESC', 
            'posts_per_page' => -1, 
            'post_status' => 'publish' 
        ));
        
                 
        
        if( $get_pages ){
                              
            foreach( $get_pages as $item ){
            	?>

            	<a href="#" data-page-id="<?php echo $item->ID;?>" <?php if(!empty($veuse_pagelist_selected_pages) && in_array($item->ID, $veuse_pagelist_selected_pages_array) ) echo 'class="active"';?>> <?php echo $item->post_title;?></a>
            	<?php
     
            }
            
        }

		?>
		</div>
		
		<input id="<?php echo $this->get_field_id( 'veuse_pagelist_selected_pages' ); ?>" name="<?php echo $this->get_field_name( 'veuse_pagelist_selected_pages' ); ?>" type="hidden" value="<?php echo esc_attr( $veuse_pagelist_selected_pages );?>" />
		

		
		<p>
			<label style="min-width:80px;" for="<?php echo $this->get_field_id('grid');?>"><?php _e('Grid:','veuse-pagelist');?></label>
			<select name="<?php echo $this->get_field_name('grid');?>">
		  		<option value="1" <?php selected( $grid, '1' , true); ?>><?php _e('1 column','veuse-pagelist');?></option>
		  		<option value="2" <?php selected( $grid, '2' , true); ?>><?php _e('2 columns','veuse-pagelist');?></option>	
		  		<option value="3" <?php selected( $grid, '3' , true); ?>><?php _e('3 columns','veuse-pagelist');?></option>	
		  		<option value="4" <?php selected( $grid, '4' , true); ?>><?php _e('4 columns','veuse-pagelist');?></option>		  
		  	</select>
		</p>
		 
		<p>
			<label style="min-width:80px;" for="<?php echo $this->get_field_id('layout');?>"><?php _e('Layout:','veuse-pagelist');?></label>
			<select name="<?php echo $this->get_field_name('layout');?>" >
		  		<option value="featured" <?php selected( $layout, 'featured' , true); ?>><?php _e('Featured','veuse-pagelist');?></option>
		  		<option value="highlights" <?php selected( $layout, 'highlights' , true); ?>><?php _e('Highlights','veuse-pagelist');?></option>	
		  		<option value="tabs" <?php selected( $layout, 'tabs' , true); ?>><?php _e('Tabs','veuse-pagelist');?></option>	
		  		<option value="accordion" <?php selected( $layout, 'accordion' , true); ?>><?php _e('Accordion','veuse-pagelist');?></option>	
		  		<option value="auto" <?php selected( $layout, 'auto' , true); ?>><?php _e('Auto','veuse-pagelist');?></option>		  
		  	</select>
		</p>
		
		
		
		<p>
		<label style="min-width:80px;" for="<?php echo $this->get_field_id( 'imagesize' ); ?>"><?php _e( "Image size:",'veuse-pagelist' ); ?></label> 
			<input size="6" id="<?php echo $this->get_field_id( 'imagesize' ); ?>" name="<?php echo $this->get_field_name( 'imagesize' ); ?>" type="text" value="<?php echo esc_attr( $imagesize ); ?>" />
			<small><?php _e( "width * height",'veuse-pagelist' ); ?></small>
		</p>
		
		<p>
		<label style="min-width:80px;" for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( "Link to page:",'veuse-pagelist' ); ?></label> 
			<input size="6" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="checkbox" <?php checked( '1', $link ); ?> />
			
		</p>
	
				<?php

	}

} 

add_action('widgets_init',create_function('','return register_widget("VeusePagelistWidget");'));
 
?>