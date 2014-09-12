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
			printf( '<a href="%s">%s</a><br />', get_permalink( $post->ID ), get_the_title() );
			
			
            the_excerpt();
            endwhile;
    }


    function nav() {
        $cats = tabkit_get_categories();
        echo '<ul class="tabkit-tabs style1">';
        foreach( $cats as $cat ) {
            printf( '<li><a href="%s">%s</a></li>', $cat['link'], $cat['name'] );
        }
        echo "</ul>";
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
