<?php


class PL_TabKit extends PageLinesSection {

    function section_scripts(){}

    function section_head() {
        add_filter( 'term_links-post_tag', array( $this, 'tag_fix' ) );
    }

    function section_template() {



        echo $this->nav();

        if( is_single() )
            $this->single();
        else
            $this->archive();

    }

    function single() {
        echo 'single';

        the_content();
        the_tags();
    }

    function archive() {
        echo 'archive';
        if( have_posts() )
            while ( have_posts() ) : the_post();
            the_content();
            endwhile;
    }


    function nav() {
        $cats = tabkit_get_categories();
        foreach( $cats as $cat ) {
            printf( '<a href="%s">%s</a><br />', $cat['link'], $cat['name'] );
        }
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
