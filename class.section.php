<?php


class PL_TabKit extends PageLinesSection {

    function section_scripts(){}

    function section_head() {
		add_filter( 'term_links-post_tag', array( $this, 'tag_fix' ) );
		add_filter( 'pless_vars', array( $this, 'add_less_vars' ) );
    }

	function section_persistent() {
		add_filter( 'pre_get_posts', array( $this, 'sort_tabs' ) );
		add_filter('pless_vars', array( $this, 'add_less_vars'));
	}

	function add_less_vars( $vars ) {
		
		$color = pl_setting( 'tk_color', array( 'default' => '337EFF' ) );
		$vars['tk-link'] = pl_hashify( $color );
		return $vars;		
	}

    function section_template() {

		global $wp_query, $paged;
		
		$cat = get_query_var('tabkit_category');
		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var('paged');
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$args = array(
			'post_type' => 'tabkit',
			'posts_per_page' => 6,
			'tabkit_category'	=> $cat,
			'paged'	=> $paged,
		);
		
		if( ! is_archive() && ! is_single() ) {
			$wp_query = new WP_Query( $args );
		}
			
        echo "<div class='section-tabkit'>";
        echo $this->nav();

        if( is_single() )
            $this->single();
        else
            $this->archive();
        echo "</div>";
    }

    function single() {
        
		if( $this->opt( 'single_meta' ) ) {
			$meta = do_shortcode( $this->opt( 'single_meta' ) );
		} else {
			$meta = sprintf( '<span class="tabkit-karma">%s</span>
            <span class="tabkit-author">by %s </span>
            <span class="tabkit-tag">tagged in %s </span>
            <span class="tabkit-time">%s </span>',
			do_shortcode( '[pl_karma icon="heart"]'),
            get_the_author(), 
            get_the_tag_list(' ', ', '), 
            get_the_time('F jS, Y') );
		}
		
        printf( '
                <div class="tabkit-post">
                    <h3>%s</h3>
                    <div class="tabkit-meta">%s</div>
                    <div class="tabkit-post-content">
                        %s
                    </div>
                
                </div><!-- end .tabkit-post -->', 
                get_the_title(),
				$meta,
                apply_filters( 'the_content', get_the_content() )
                );
    }

    function archive() {
        // echo 'archive<br />';
        if( have_posts() )
            while ( have_posts() ) : the_post();
			global $post;

            $num_comments = get_comments_number();
            if ( comments_open() ) {
                if ( $num_comments == 0 ) {
                    $comments = __('No Comments');
                } elseif ( $num_comments > 1 ) {
                    $comments = $num_comments . __(' Comments');
                } else {
                    $comments = __('1 Comment');
                }
                $tabkit_comments = $comments;
            } else {
                $tabkit_comments =  __('Comments are off for this post.');
            }

			if( $this->opt( 'archive_meta' ) ) {
				$meta = do_shortcode( $this->opt( 'archive_meta' ) );
			} else {
				$meta = sprintf( '<span class="tabkit-karma">%s</span>
	            				<span class="tabkit-author">by %s </span>
	            				<span class="tabkit-tag">tagged in %s </span>
	            				<span class="tabkit-comments">%s </span>
	            				<span class="tabkit-time">%s </span>',
				do_shortcode( '[pl_karma icon="heart"]'),
	            get_the_author(), 
	            get_the_tag_list(' ', ', '), 
	            $tabkit_comments,
	            get_the_time('F jS, Y')
	 		);
			}

			printf( '
                <div class="tabkit-post">
                    <h3><a href="%s">%s</a></h3>
                    <span class="tabkit-icon">
                       <span class="icon-stack">
                            <i class="icon icon-circle icon-stack-2x icon-3x"></i>
                            <i class="icon fa-file-text-o icon-stack-1x icon-inverse"></i>
                        </span> 
                    </span>
                    <div class="tabkit-meta">%s</div>             
                </div><!-- end .tabkit-post -->', 
                get_permalink( $post->ID ), 
                get_the_title(),
				$meta
                );

            endwhile;

			pagelines_pagination();
    }


    function nav() {
	
		$classes = array(
			'new'	=> '',
			'trending'	=> '',
			'popular'	=> ''
		);

	
		if( isset( $_REQUEST['sort_by'] ) ) {
			$classes[$_REQUEST['sort_by']] = 'current';
		} else {
			$classes['new'] = 'current';
		}
			
		
		
        $cats = tabkit_get_categories();

		$o = get_queried_object( );

        echo '<div class="filter-bar">
                <ul class="tabkit-filters style1">';
        foreach( $cats as $cat ) {
			$cat_current = '';
	
			if( $cat['name'] == $o->name )
				$cat_current = 'current';
	
            printf( '<li class="%s"><a href="%s">%s</a></li>', $cat_current, $cat['link'], $cat['name'] );
        }
        echo '</ul>';

		echo $this->get_sorting( $classes );
		   
        echo '</div><!-- end .filter-bar -->';
    }

	function get_sorting( $classes ) {
	
		$out = '';
		$sorts = array(
			'new'	=> __( 'New', 'pagelines' ),
			'trending'	=> __( 'Trending', 'pagelines' ),
			'popular'	=> __( 'Popular', 'pagelines' )
		);
		
		foreach( $sorts as $k => $sort ) {
			if( '1' !== $this->opt( "disable_$k" ) ) {
				$out .= sprintf( '<li class="%s"><a href="%s">%s</a></li>',
					$classes[$k],
					add_query_arg( array( 'post_type' => 'tabkit', 'sort_by' => $k ), site_url() ),
					$sort
					);
			}
		}

		if( $out ) {
			return sprintf( '<ul class="tabkit-secondary-filters style1">%s</ul>', $out );
		}
	}

    function section_opts(){
	
		$opts = array(

			array(
				'type'	=> 'multi',
				'key'	=> 'tabkit',
				'title'	=> __( 'Meta Config', 'pagelines' ),
				'col'	=> 1,
				'opts'	=> array(
					array(
						'type'	=> 'text',
						'key'	=> 'archive_meta',
						'label'	=> __( 'Custom Archive Meta', 'pagelines' )
					),
					array(
						'type'	=> 'text',
						'key'	=> 'single_meta',
						'label'	=> __( 'Single Meta', 'pagelines' )
						
					)
				)
			),
			array(
				'type'	=> 'multi',
				'key'	=> 'tabkit2',
				'title'	=> __( 'Sorting Options', 'pagelines' ),
				'col'	=> 2,
				'opts'	=> array(
					array(
						'type'	=> 'check',
						'key'	=> 'disable_new',
						'default'	=> false,
						'label'		=> __( 'Disable NEW')
					),
					array(
						'type'	=> 'check',
						'key'	=> 'disable_trending',
						'default'	=> false,
						'label'		=> __( 'Disable TRENDING')
					),
					array(
						'type'	=> 'check',
						'key'	=> 'disable_popular',
						'default'	=> false,
						'label'		=> __( 'Disable POPULAR')
					),
					array(
						'type'	=> 'color',
						'key'	=> 'tk_color',
						'label'	=> __( 'Header Color', 'pagelines' ),
						'default'	=> '337EFF',
						'scope'	=> 'global'
						
					)
				)
			)


		);
		
		return $opts;
    }

	function sort_tabs( $query ) {
		
		if( is_admin() )
			return $query;

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var('paged');
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query->set( 'posts_per_page', get_option( 'posts_per_page' ) );
		$query->set( 'paged', $paged );

		if( isset( $_REQUEST['sort_by'] ) && 'popular' == $_REQUEST['sort_by'] ) {
			$query->set('meta_key', '_pl_karma');
			$query->set('order', 'DESC');
			$query->set('orderby','meta_value_num');
		}
		
		if( isset( $_REQUEST['sort_by'] ) && 'trending' == $_REQUEST['sort_by'] ) {

			$comment_posts = array();

			$comments_query = new WP_Comment_Query;
			
			$args = array(
			 	'date_query' => array(
				        array(
				            'after' => '1 month ago',
				        ),
				    )
			);
					
			$comments = $comments_query->query( $args );
			foreach( $comments as $k => $comment ) {
				$comment_posts[] = $comment->comment_post_ID;
			}
			$query->set( 'orderby', 'comment_count' );
			$query->set ( 'post__in', $comment_posts ); 
		}		
	}

    function tag_fix( $links ) {

        foreach( $links as $k => $link ) {
            $links[$k] = preg_replace( '/href=["\']?([^"\'>]+)["\']?/', 'href="$1?post_type=tabkit"', $link , -1 );
        }
        return $links;
    }
}
