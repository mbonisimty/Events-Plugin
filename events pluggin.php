<?php
/*
Plugin Name: Events Plugin
Plugin URI:
Description: This is a plugin designed to test the fundamentals required for WordPress Engineers to work at FlickerLeap. This test is continuously being updated to further test the fundamentals of being a WordPress Engineer.
Author: Mbonisi Tshuma - Code Assasin in Training
Author URI: https://github.com/mbonisi287/
License: GPL2
License: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: Mbonisi
*/
/* !0. TABLE OF CONTENTS */
/*	
	1. HOOKS
		1.1 - register all our custom shortcodes
		1.2 - register custom admin column headers
		1.3 - register custom admin column data
	2. SHORTCODES

	3. FILTERS
		3.1 - your_post_edit_columns()
		3.2 - add_your_fields_meta_box()
		
	4. EXTERNAL SCRIPTS
	5. ACTIONS
		5.1 - show_your_fields_meta_box()
		5.2 - save_your_fields_meta()
	6. HELPERS
		6.1 - your_post_custom_columns()
	7. CUSTOM POST TYPES
		7.1 - create_post_your_post()
	8. ADMIN PAGES
	9. SETTINGS
*/ 

/*	1. HOOKS	*/
//	1.1 
//	hint: register all our custom shortcodes

//	1.2 
//	hint: register custom admin column headers
add_filter( "manage_edit-your_post_columns" , "your_post_edit_columns" );

//	1.3
//	hint: register my own custom admin column data
add_action( "manage_your_post_posts_custom_column", "your_post_custom_columns" );
add_action( 'add_meta_boxes', 'add_your_fields_meta_box' );
add_action( 'save_post', 'save_your_fields_meta' );
add_action( 'init', 'create_post_your_post' );

/*	2. SHORTCODES  */

/*	3. FILTERS  */
//	3.1 
//	hint: shows custom column headers
function your_post_edit_columns ($columns){
	
		global $post;  
		$meta = get_post_meta( $post->ID, 'your_fields', true );
	
	 $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => "Event",
        "your_fields_ev_desc" => "Description",
        "your_fields_ev_cat" => "Event Category",  
        "your_fields_ev_date" => "Event Dates",
    	"your_fields_tk_price" => "Ticket Price",
		"your_fields_tk_avail" => "Tickets Available",
     
        
        );
    return $columns;
	
}

//	3.2 
//	hint: show the custom post fields
function add_your_fields_meta_box() {
	add_meta_box(
		'your_fields_meta_box', // $id
		'Tickets', // $title
		'show_your_fields_meta_box', // $callback
		'your_post', // $screen
		'normal', // $context
		'high' // $priority
	);
}

/*	4. EXTERNAL SCRIPTS  */

/*	5. ACTIONS  */
//	5.1
//	hint: shows the fields in the meta box 
function show_your_fields_meta_box() {
	
	global $post;   
	$meta = get_post_meta( $post->ID, 'your_fields', true );	
	?>

	<input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

    <!-- All fields will to datat retrieved will go here -->
		<p>
			<label for="your_fields[date]">Event Date</label>
			<br>
			<input type="date" name="your_fields[date]" id="your_fields[date]" 
			class="regular-text"  value="<?php echo $meta['date'] ?>">
		</p>
		
		<p>
			<label for="your_fields[number]">Ticket Price</label>
			<br>
			<input type="number" name="your_fields[price]" min="0" 
			id="your_fields[price]" placeholder="R" value="<?php echo $meta['price']; ?>" >
		</p>	
	
		<p>
			<label for="your_fields[text]">Tickets Available</label>
			<br>
			<input type="number" min="0" placeholder="0" 
			name="your_fields[tickets]" id="your_fields[tickets]" 
			class="regular-text" value="<?php echo $meta['tickets']; ?>">
		</p>
<?php

}
//	5.2
//	hint: saves the data in the meta box    
function save_your_fields_meta( $post_id ) {   
	// verify nonce
	if ( !wp_verify_nonce( $_POST['your_meta_box_nonce'], basename(__FILE__) ) ) {
		return $post_id; 
	}
	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	// check permissions
	if ( 'page' === $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}  
	}
	
	$old = get_post_meta( $post_id, 'your_fields', true );
	$new = $_POST['your_fields'];

	if ( $new && $new !== $old ) {
		update_post_meta( $post_id, 'your_fields', $new );
	} elseif ( '' === $new && $old ) {
		delete_post_meta( $post_id, 'your_fields', $old );
	}
}


/*	6. HELPERS  */
//	6.1 
//	hint: retreives the column data for the post type
function your_post_custom_columns($column){
	
		global $post;  
		$meta = get_post_meta( $post->ID, 'your_fields', true ); ?>
		
 <input type="hidden" name="your_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

<?php
	switch ($column)
	{
		case "your_fields_ev_date";
			echo $meta['date'];
		break;
		
		case "your_fields_tk_price";
			echo $meta['price'];
		break;
		
		case "your_fields_tk_avail";
			echo $meta['tickets'];
		break;
		
		case "your_fields_ev_desc";
            the_excerpt();
        break;
		
		case "your_fields_ev_cat":
            // - show taxonomy terms -
            $eventcats = get_the_terms($post->ID, "category");
            $eventcats_html = array();
            if ($eventcats) {
            foreach ($eventcats as $eventcat)
            array_push($eventcats_html, $eventcat->name);
            echo implode($eventcats_html, ", ");
            } else {
            _e('None', 'themeforce');;
            }
        break;
		
	}
	
}

/*	7. CUSTOM POST TYPES  */
//	7.1 
//	hint: creates my custom post type
function create_post_your_post() {
	register_post_type( 'your_post',
		array(
			'labels'       => array(
				'name'       => __( 'Events' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'has_archive'  => true,
			'supports'     => array(
				'title',
				'editor',
				'excerpt',
				'thumbnail',
			), 
			'taxonomies'   => array(
				'post_tag',
				'category',
			)
		)
	);
	register_taxonomy_for_object_type( 'category', 'your_post' );
	register_taxonomy_for_object_type( 'post_tag', 'your_post' );
}
/* 8. ADMIN PAGES  */

/* 9. SETTINGS  */
	
?>
