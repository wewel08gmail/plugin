<?php

//hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_page_categories_hierarchical_taxonomy', 0 );

//create a custom taxonomy name it topics for your posts
function create_page_categories_hierarchical_taxonomy() {
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
  $labels = array(
    'name' => _x( 'Categories', 'taxonomy general name' ),
    'singular_name' => _x( 'Category', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Category' ),
    'all_items' => __( 'All Categories' ),
    'parent_item' => __( 'Parent Category' ),
    'parent_item_colon' => __( 'Parent Category:' ),
    'edit_item' => __( 'Edit Category' ),
    'update_item' => __( 'Update Category' ),
    'add_new_item' => __( 'Add New Category' ),
    'new_item_name' => __( 'New Category Name' ),
    'menu_name' => __( 'Categories' ),
  );   
 
// Now register the taxonomy for page category

  register_taxonomy('category',array('page'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'category' ),
  ));
}




/* add, edit, delete and save custom color metadata for category with colorpicker */
//register term meta
function jt_register_meta() {
    register_meta( 'term', 'color', 'jt_sanitize_hex' );
}
// make sure that we have a valid color hex code.
function jt_sanitize_hex( $color ) {
    $color = ltrim( $color, '#' );
    return preg_match( '/([A-Fa-f0-9]{3}){1,2}$/', $color ) ? $color : '';
}

//get term meta
function jt_get_term_color( $term_id, $hash = false ) {
    $color = get_term_meta( $term_id, 'color', true );
    $color = jt_sanitize_hex( $color );
    return $hash && $color ? "#{$color}" : $color;
}

//add form fields
add_action( 'category_add_form_fields', 'ccp_new_term_color_field' );
function ccp_new_term_color_field() {
    wp_nonce_field( basename( __FILE__ ), 'jt_term_color_nonce' ); ?>

    <div class="form-field jt-term-color-wrap">
        <label for="jt-term-color"><?php _e( 'Color', 'jt' ); ?></label>
        <input type="text" name="jt_term_color" id="jt-term-color" value="" class="jt-color-field" data-default-color="#ffffff" />
    </div>
<?php }
add_action( 'category_edit_form_fields', 'ccp_edit_term_color_field' );
function ccp_edit_term_color_field( $term ) {
    $default = '#ffffff';
    $color   = jt_get_term_color( $term->term_id, true );
    if ( ! $color )
        $color = $default; ?>

    <tr class="form-field jt-term-color-wrap">
        <th scope="row"><label for="jt-term-color"><?php _e( 'Color', 'jt' ); ?></label></th>
        <td>
            <?php wp_nonce_field( basename( __FILE__ ), 'jt_term_color_nonce' ); ?>
            <input type="text" name="jt_term_color" id="jt-term-color" value="<?php echo esc_attr( $color ); ?>" class="jt-color-field" data-default-color="<?php echo esc_attr( $default ); ?>" />
        </td>
    </tr>
<?php }


//save term meta
add_action( 'edit_category',   'jt_save_term_color' );
add_action( 'create_category', 'jt_save_term_color' );
function jt_save_term_color( $term_id ) {
    if ( ! isset( $_POST['jt_term_color_nonce'] ) || ! wp_verify_nonce( $_POST['jt_term_color_nonce'], basename( __FILE__ ) ) )
        return;
    $old_color = jt_get_term_color( $term_id );
    $new_color = isset( $_POST['jt_term_color'] ) ? jt_sanitize_hex( $_POST['jt_term_color'] ) : '';
    if ( $old_color && '' === $new_color )
        delete_term_meta( $term_id, 'color' );
    else if ( $old_color !== $new_color )
        update_term_meta( $term_id, 'color', $new_color );
}

//add a term meta column
add_filter( 'manage_edit-category_columns', 'jt_edit_term_columns' );
function jt_edit_term_columns( $columns ) {
    $columns['color'] = __( 'Color', 'jt' );
    return $columns;
}
// Handle the output for the column
add_filter( 'manage_category_custom_column', 'jt_manage_term_custom_column', 10, 3 );
function jt_manage_term_custom_column( $out, $column, $term_id ) {
    if ( 'color' === $column ) {
        $color = jt_get_term_color( $term_id, true );
        if ( ! $color )
            $color = '#ffffff';
        $out = sprintf( '<span class="color-block" style="background:%s;">&nbsp;</span>', esc_attr( $color ) );
    }
    return $out;
}

//enqueue color picker
add_action( 'admin_enqueue_scripts', 'jt_admin_enqueue_scripts' );
function jt_admin_enqueue_scripts( $hook_suffix ) {
    if ( 'edit-tags.php' !== $hook_suffix || 'category' !== get_current_screen()->taxonomy )
        return;
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    add_action( 'admin_head',   'jt_term_colors_print_styles' );
    add_action( 'admin_footer', 'jt_term_colors_print_scripts' );
}
function jt_term_colors_print_styles() { ?>

    <style type="text/css">
        .column-color { width: 50px; }
        .column-color .color-block { display: inline-block; width: 28px; height: 28px; border: 1px solid #ddd; }
    </style>
<?php }
function jt_term_colors_print_scripts() { ?>

    <script type="text/javascript">
        jQuery( document ).ready( function( $ ) {
            $( '.jt-color-field' ).wpColorPicker();
        } );
    </script>
<?php }

/* end  add, edit, delete and save custom color metadata for category with colorpicker */