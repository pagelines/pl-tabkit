<?php


class PL_TabKit extends PageLinesSection {

    function section_scripts(){}

    function section_head() {
        add_filter( 'term_links-post_tag', array( $this, 'tag_fix' ) );
    }

    function section_template() {


        echo "<div class='section-tabkit'>";
        echo $this->nav();

        if( is_single() )
            $this->single();
        else
            $this->archive();
        echo "</div>";

    }

    function single() {
        // echo 'single';

        the_content();
        the_tags();
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

			printf( '
                <div class="tabkit-post">
                    <h3><a href="%s">%s</a></h3>
                    <span class="tabkit-icon"><i class="icon icon-file-code-o"></i></span>
                    <div class="tabkit-meta">
                        <span class="pl-karma">9999999</span>
                        <span class="tabkit-author">by %s </span>
                        <span class="tabkit-tag">tagged in %s </span>
                        <span class="tabkit-comments">%s </span>
                        <span class="tabkit-time">%s </span>
                    </div>


                
                </div><!-- end .tabkit-post -->', 
                get_permalink( $post->ID ), 
                get_the_title(), 
                get_the_author(), 
                get_the_tag_list(' ', ', '), 
                $tabkit_comments,
                get_the_time('F jS, Y') 
                );

            endwhile;
    }


    function nav() {
        $cats = tabkit_get_categories();
        echo '<div class="filter-bar">
                <ul class="tabkit-filters style1">';
        foreach( $cats as $cat ) {
            printf( '<li><a href="%s">%s</a></li>', $cat['link'], $cat['name'] );
        }
        echo '
          </ul>
          <ul class="tabkit-secondary-filters style1">
            <li class="current"><a href="#">New</a></li><li><a href="#">Trending</a></li><li><a href="#">Popular</a></li>
          </ul>
        </div><!-- end .filter-bar -->
        ';
    }

    function section_opts(){
        return array();
    }

    function tag_fix( $links ) {

        foreach( $links as $k => $link ) {
            $links[$k] = preg_replace( '/href=["\']?([^"\'>]+)["\']?/', 'href="$1?post_type=tabkit"', $link , -1 );
        }
        return $links;
    }
}
