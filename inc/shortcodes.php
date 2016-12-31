<?php
if (!function_exists('mp_portfolio_cb')):
    function mp_portfolio_cb( $atts ) {
    	$atts = shortcode_atts( array(
    		'count' => 12
    	), $atts );

    	global $post;

    	global $wp_query;

    	$paged = get_query_var('paged', 1);

    	$args = array(
    		'post_type' => 'mp-portfolio',
    		'posts_per_page' => $atts['count'],
    		'paged' => $paged
    	);

    	$query = new WP_Query($args);

    	$terms = get_terms( 'cat_portfolio' );

    	ob_start();
    	?>
    	<div class="mpp-filters">
    		<ul class="clearfix">
    			<li><a href="#" class="active" data-filter="*"><?php esc_html_e( 'All', 'mighty-portfolio' ); ?></a></li>
    			<?php foreach($terms as $term): ?>
    				<li><a href="#" data-filter=".<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
    			<?php endforeach; ?>
    		</ul>
    	</div>
    	<div class="mpp-items clearfix">
    		<?php if($query->have_posts()): while ( $query->have_posts() ) : $query->the_post(); ?>
    			<?php
    				$cats = get_the_terms(get_the_id(), 'cat_portfolio');

    				$cls = array();

    				if(!empty($cats)){
    					foreach($cats as $cat){
    						$cls[] = $cat->slug;
    					}
    				}
    			?>
    			<div class="mpp-item <?php echo implode(' ', $cls) ?>">
    				<a href="<?php the_permalink(); ?>">
    					<?php echo wp_get_attachment_image(get_post_thumbnail_id(get_the_id()), 'mp_gallery_thumb'); ?>
    					<span class="mpp-item-overlayer"></span>
    					<span class="mpp-item-info">
    						<span class="mpp-item-title"><?php the_title(); ?></span>
    						<span class="mpp-item-cats">
    						<?php
    							
    							if(!empty($cats)){
    								$i = 0;
    								foreach($cats as $cat){
    									if ($i) {
    										echo ", ";
    									}
    									echo $cat->name;

    									$i++;
    								}
    							}
    						?>
    						</span>
    					</span>
    				</a>
    			</div>
    		<?php endwhile; endif; ?>
    	</div>
    	<?php if ( $query->max_num_pages > 1 ): ?>
    		<nav class="navigation mpp-pagination" role="navigation">
    			<div class="nav-links"><?php echo mpp_paginate_links($query); ?></div>
    		</nav>
    	<?php endif; ?>
    	<?php
    	wp_reset_postdata();


    	return ob_get_clean();
    }
    add_shortcode( 'mp_portfolio','mp_portfolio_cb' );
endif;


if (!function_exists('mpp_paginate_links')):

    function mpp_paginate_links( $wp_query, $args = '' ) {
        global $wp_rewrite;
     
        // Setting up default values based on the current URL.
        $pagenum_link = html_entity_decode( get_pagenum_link() );
        $url_parts    = explode( '?', $pagenum_link );
     
        // Get max pages and current page out of the current query, if available.
        $total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
        $current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
     
        // Append the format placeholder to the base URL.
        $pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';
     
        // URL base depends on permalink settings.
        $format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
        $format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';
     
        $defaults = array(
            'base' => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
            'format' => $format, // ?page=%#% : %#% is replaced by the page number
            'total' => $total,
            'current' => $current,
            'show_all' => false,
            'prev_next' => true,
            'prev_text' => __('&laquo; Previous', 'mighty-portfolio'),
            'next_text' => __('Next &raquo;', 'mighty-portfolio'),
            'end_size' => 1,
            'mid_size' => 2,
            'type' => 'plain',
            'add_args' => array(), // array of query args to add
            'add_fragment' => '',
            'before_page_number' => '',
            'after_page_number' => ''
        );
     
        $args = wp_parse_args( $args, $defaults );
     
        if ( ! is_array( $args['add_args'] ) ) {
            $args['add_args'] = array();
        }
     
        // Merge additional query vars found in the original URL into 'add_args' array.
        if ( isset( $url_parts[1] ) ) {
            // Find the format argument.
            $format = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
            $format_query = isset( $format[1] ) ? $format[1] : '';
            wp_parse_str( $format_query, $format_args );
     
            // Find the query args of the requested URL.
            wp_parse_str( $url_parts[1], $url_query_args );
     
            // Remove the format argument from the array of query arguments, to avoid overwriting custom format.
            foreach ( $format_args as $format_arg => $format_arg_value ) {
                unset( $url_query_args[ $format_arg ] );
            }
     
            $args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
        }
     
        // Who knows what else people pass in $args
        $total = (int) $args['total'];
        if ( $total < 2 ) {
            return;
        }
        $current  = (int) $args['current'];
        $end_size = (int) $args['end_size']; // Out of bounds?  Make it the default.
        if ( $end_size < 1 ) {
            $end_size = 1;
        }
        $mid_size = (int) $args['mid_size'];
        if ( $mid_size < 0 ) {
            $mid_size = 2;
        }
        $add_args = $args['add_args'];
        $r = '';
        $page_links = array();
        $dots = false;
     
        if ( $args['prev_next'] && $current && 1 < $current ) :
            $link = str_replace( '%_%', 2 == $current ? '' : $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current - 1, $link );
            if ( $add_args )
                $link = add_query_arg( $add_args, $link );
            $link .= $args['add_fragment'];
     
            /**
             * Filters the paginated links for the given archive pages.
             *
             * @since 3.0.0
             *
             * @param string $link The paginated link URL.
             */
            $page_links[] = '<a class="prev page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['prev_text'] . '</a>';
        endif;
        for ( $n = 1; $n <= $total; $n++ ) :
            if ( $n == $current ) :
                $page_links[] = "<span class='page-numbers current'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</span>";
                $dots = true;
            else :
                if ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
                    $link = str_replace( '%_%', 1 == $n ? '' : $args['format'], $args['base'] );
                    $link = str_replace( '%#%', $n, $link );
                    if ( $add_args )
                        $link = add_query_arg( $add_args, $link );
                    $link .= $args['add_fragment'];
     
                    /** This filter is documented in wp-includes/general-template.php */
                    $page_links[] = "<a class='page-numbers' href='" . esc_url( apply_filters( 'paginate_links', $link ) ) . "'>" . $args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number'] . "</a>";
                    $dots = true;
                elseif ( $dots && ! $args['show_all'] ) :
                    $page_links[] = '<span class="page-numbers dots">' . __( '&hellip;' ) . '</span>';
                    $dots = false;
                endif;
            endif;
        endfor;
        if ( $args['prev_next'] && $current && $current < $total ) :
            $link = str_replace( '%_%', $args['format'], $args['base'] );
            $link = str_replace( '%#%', $current + 1, $link );
            if ( $add_args )
                $link = add_query_arg( $add_args, $link );
            $link .= $args['add_fragment'];
     
            /** This filter is documented in wp-includes/general-template.php */
            $page_links[] = '<a class="next page-numbers" href="' . esc_url( apply_filters( 'paginate_links', $link ) ) . '">' . $args['next_text'] . '</a>';
        endif;
        switch ( $args['type'] ) {
            case 'array' :
                return $page_links;
     
            case 'list' :
                $r .= "<ul class='page-numbers'>\n\t<li>";
                $r .= join("</li>\n\t<li>", $page_links);
                $r .= "</li>\n</ul>\n";
                break;
     
            default :
                $r = join("\n", $page_links);
                break;
        }
        return $r;
    }

endif;