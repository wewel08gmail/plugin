<?php
global $page, $post;

function plugin_enqueue_select_input() {
	wp_enqueue_style( 'select_2_css',  'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css' );
    wp_enqueue_script( 'select_2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js' );
	wp_enqueue_style( 'rs_user_style',  plugins_url( '/css/rs-user-styles.css' , __FILE__ ) );
	wp_enqueue_script( 'rs_data_fetch', plugins_url( '/js/rs-data-fetch.js' , __FILE__ ) );
	
	echo '<script type="text/javascript"> var siteUrl = "'.site_url().'";</script>';
	
}
add_action('wp_footer', 'plugin_enqueue_select_input');
//add_action( 'wp_enqueue_scripts', 'plugin_enqueue_select_input' );

//display form page filter
function rs_page_form_filter( $atts ) {
	//print_r($terms);

	
$terms = get_terms( array(
		'taxonomy' => 'category',
		'hide_empty' => false,
	) );
	$rs_select_data='';
	$rs_select_data.='<div class="rs-select-wrapper"><select class="js-multiple-category" multiple="multiple">';

	foreach($terms as $term){

		$rs_select_data.='<option data-color="'.get_term_meta($term->term_id, 'color', true).'" data-page-id="'.get_the_ID().'" value="'.$term->term_id.'">'.$term->name.'</option>';
		//print(get_term_meta($term->term_id, 'color', true));
	}

	$rs_select_data.='</select></div>';
	$rs_select_data.='<div class="date_control_wrapper">';
	$time = strtotime("-2 year", time());
    $dateyearsago = date("Y-m-d", $time);
	$rs_select_data.='<div class="start_date_wrapper">Start Date:<br /> <input type="date" name="start_date" id="start_date" value="'.$dateyearsago.'" /></div>';
	$rs_select_data.='<div class="end_date_wrapper">End Date:<br /> <input type="date" name="end_date" id="end_date" value="'.date('Y-m-d').'" /></div>';
	$rs_select_data.='</div>';
	//$rs_select_data.='<div class="view_tickbox_wrapper"><input type="checkbox" name="unviewed" id="unviewed" value="unviewed"><label for="unviewed">Unviewed only</label></div>';
	$rs_select_data.='<div class="page-viewed-tick wct-tickbox wct-tickbox-unticked">
					<span class="wct-tick-icon"></span>
					<span class="wct-tick-text wct-tick-text-unticked">Unviewed Only</span>
					<span class="wct-tick-text wct-tick-text-ticked">Unviewed pages only.</span>
				</div>';
	$rs_select_data.='<div class="navigation-sidebar navigation-sidebar-2"><ul id="rs-data-list">';

	/*
	   $page_parent=wp_get_post_parent_id($post->ID);
	   $rs_select_data.= wp_list_pages( array(
			'child_of' => $page_parent, // Only pages that are children of the current page
			'depth' => 1 ,   // Only show one level of hierarchy
			'sort_order' => 'asc',
			'title_li' => NULL,
			'echo' => 0
	   ));

	  */ 


	$page_parent=wp_get_post_parent_id($post->ID);
	
	$args = array(
		'post_type'      => 'page',
		'posts_per_page' => -1,
		'post_parent'    => $page_parent,
		'order'          => 'ASC',
		'orderby'        => 'menu_order'
	 );
	 
	$parent = new WP_Query( $args );
	
	if ( $parent->have_posts() ) : ?>
	
		<?php while ( $parent->have_posts() ) : $parent->the_post(); ?>
		   <?php 
		   $page_title = get_the_title( $post->id );
		   $page_permalink = get_the_permalink( $post->id );
		   $page_categories = get_the_category($post->id);	   
		   if(count($page_categories) != 0){
			   $page_cirle_dot = '';
			   foreach($page_categories as $page_category){		   			  
				  $category_color = get_term_meta($page_category->term_id,'color',true);
				  $page_cirle_dot.='<div class="circle" style="background:#'.$category_color.'">&nbsp;</div>';															
			   }
			   $rs_select_data.="<li><div class='rs-category-link'><a href='".$page_permalink."'>".$page_title."</a></div><div class='rs-category-color'>".$page_cirle_dot."</div></li>";
		   }else{
			 $rs_select_data.="<li><a href='".$page_permalink."'>".$page_title."</a></li>";  
		   }
		   
			?>
		<?php endwhile; ?>
	
	<?php endif; wp_reset_query(); 



	$rs_select_data.='</ul></div>';
	


	return $rs_select_data;


}
//display categories in a page
function rs_page_category_display(){
	$rs_category_data = '';	
	
	$rs_category_data.='<div class="rs_category_wrapper"><ul>';
	
	$page_categories = get_the_category($post->id);	

	foreach($page_categories as $page_category){		   			  
		  $category_color = get_term_meta($page_category->term_id,'color',true);
		  $rs_category_data.='<li class="rs_category" style="background:#'.$category_color.'">'.$page_category->name.'</li>';															
	}
	
	$rs_category_data.='</ul></div>';
	
	return $rs_category_data;
}

add_shortcode( 'rsform_filter', 'rs_page_form_filter' );
add_shortcode( 'rspage_category', 'rs_page_category_display' );