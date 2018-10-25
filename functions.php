<?php
	
  /*********************************************/

	if ( ! shortcode_exists( 'list_of_pages' ) ) {
    	
		function list_of_pages_func( $atts ) {
			global $post;
			if (is_page()) {
				$current_id = $post->ID;
				$output = "";

				$default_theme = "<li id=\"lof_page_{page_id}\" class=\"lof_page {page_active_class}\"><a href=\"{link}\" target=\"{link_target}\">{title}</a></li>";

				$a = shortcode_atts( array(
					'theme' => $default_theme,						// custom theme
					'pre_code'		=>	'<ul class="list_of_pages">',		// pre code
					'post_code'		=>	'</ul>',				// post code
					'link_target' 		=>	'_self',				// open in new target
					'if_active'		=>	'lof_page_active',			// if current page - add `active` class
					'widget_pre_title'	=>	'<h4 class=\"widgettitle\">',		// widget pre title code
					'widget_post_title'	=>	'</h4>',				// widget post title code
					'widget_title'		=>	null,					// widget title, default null
					'if_null_return'	=>	null,					// display message if no child page found
					'order_by'		=>	'menu_order',				// order pages by field
					'order'			=>	'ASC',					// ASC or DESC order
				), $atts );

				$args = array(
					'post_type'		=>		'page',
					'numberposts'		=>		-1,
					'post_status'		=>		'publish',
					'orderby' 		=> 		$a['order_by'], 
					'order'			=>		$a['order'],
				);

				if ( is_page() && $post->post_parent ) {	// subpage
					$args['post_parent'] = $post->post_parent;
				} else {									// not subpage
					$args['post_parent'] = $current_id;
				}

				$child_pages = get_children( $args );

				if ($child_pages) {
					foreach ($child_pages as $page) {
						if ($page->ID) {
							$id = $page->ID;
							$url = get_permalink($page->ID);
							$title = $page->post_title;
							$template = $a['theme'];

							// add title
							$template = str_replace("{title}", $title, $template);
							// add url
							$template = str_replace("{link}", $url, $template);

							// add url target param
							$url_target = $a['link_target'];
							$template = str_replace("{link_target}", $url_target, $template);

							// add id
							$template = str_replace("{page_id}", $id, $template);

							// add active class
							$page_active = $a['if_active'];
							if ($current_id == $id) {
								$template = str_replace("{page_active_class}", $page_active, $template);
							} else {
								$template = str_replace("{page_active_class}", "", $template);
							}

							$output .= $template;
						}
					}

					// adding pre and post code
					$pre_code = $a['pre_code'];
					$post_code = $a['post_code'];
					$output = $pre_code . $output . $post_code;

					// add widget title
					if ($a['widget_title'] != null) {
						$output = $a['widget_pre_title'] . $a['widget_title'] . $a['widget_post_title'] . $output;
					}
					

				} else {
					if ($a['if_null_return'] != null) {
						return $a['if_null_return'];
					} 
					else {
						return null;
					}
				}

				return $output;
			} else {
				return null;	// not page
			}
		}
		add_shortcode( 'list_of_pages', 'list_of_pages_func' );
	}
  
  /*********************************************/
  
?>
