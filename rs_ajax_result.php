<?php 

if (file_exists("../../../wp-load.php")){
    require_once("../../../wp-load.php");
}
	
global $post;


$rs_select_data ='';

$post_id = $_REQUEST['pid'];
$categories = $_REQUEST['select'];
$start_date = $_REQUEST['startdate'];
$end_date = $_REQUEST['enddate'];
$read_status = $_REQUEST['viewstatus'];

$page_parent=wp_get_post_parent_id($post_id);

if($categories!=""){ 
	
	$args = array(
		'post_type'      => 'page',
		'posts_per_page' => -1,
	//    'post_parent'    => $page_parent,
		'date_query' => array(
			array(		    
				'after'     =>  array(
					'year'  => date("Y", strtotime($start_date)),
					'month' => date("m", strtotime($start_date)),
					'day'   => date("d", strtotime($start_date)),
				),
				'before'    => array(
					'year'  => date("Y", strtotime($end_date)),
					'month' => date("m", strtotime($end_date)),
					'day'   => date("d", strtotime($end_date)),
				),
				'inclusive' => true,
			),
		),
		'order'          => 'ASC',
		'orderby'        => 'menu_order'
	 );

}else{ // if no category query to get child pages
	
	$args = array(
		'post_type'      => 'page',
		'posts_per_page' => -1,
	    'post_parent'    => $page_parent,
		'date_query' => array(
			array(		    
				'after'     =>  array(
					'year'  => date("Y", strtotime($start_date)),
					'month' => date("m", strtotime($start_date)),
					'day'   => date("d", strtotime($start_date)),
				),
				'before'    => array(
					'year'  => date("Y", strtotime($end_date)),
					'month' => date("m", strtotime($end_date)),
					'day'   => date("d", strtotime($end_date)),
				),
				'inclusive' => true,
			),
		),
		'order'          => 'ASC',
		'orderby'        => 'menu_order'
	 );
	 
}

$parent = new WP_Query( $args );

if ( $parent->have_posts() ) : ?>

    <?php while ( $parent->have_posts() ) : $parent->the_post();
        
	   $page_title = get_the_title( $post->id );
	   $page_permalink = get_the_permalink( $post->id );
	   $page_categories = get_the_category($post->id);
	   
	   $current_query_post_id = get_the_ID();
	   $current_user = wp_get_current_user();
	   $rs_read_status=get_user_meta($current_user->ID, 'rs_user_read_status_'.$current_query_post_id, true);	   

	   if($categories!=""){
	   
		   foreach($page_categories as $page_category){		   
			   $display = false;
			   
				   foreach($categories as $category){
					 if($category==$page_category->term_id){
						$page_cirle_dot = '';
						foreach($page_categories as $page_category){ // loop to get categories color
							$category_color = get_term_meta($page_category->term_id,'color',true);
							$page_cirle_dot.='<div class="circle" style="background:#'.$category_color.'">&nbsp;</div>';												
						}
						if($read_status=='unviewed'){
							if($rs_read_status!=1){
								$rs_select_data.="<li><div class='rs-category-link'><a href='".$page_permalink."'>".$page_title."</a></div><div class='rs-category-color'>".$page_cirle_dot."</div></li>"; 
								$display = true;
								break;
							}							
						}else{
							$rs_select_data.="<li><div class='rs-category-link'><a href='".$page_permalink."'>".$page_title."</a></div><div class='rs-category-color'>".$page_cirle_dot."</div></li>"; 
						    //do nothing
							$display = true;
							break;			
						}
					 }    
					 
				   }
				 if($display) //if page is displayed in the result list - stop  
				  break;			   			 
		   }
	   
	   
	   }else{
		   $rs_select_data.="<li><a href='".$page_permalink."'>".$page_title."</a></li>";		   
	   }	   

     endwhile; 

endif; 
wp_reset_query(); 

echo $rs_select_data;
?>