<?php
// main class

class PL_TabKit_Main {

    function __construct() {

        add_action( 'after_setup_theme', array( $this, 'setup' ) );
    }

    function setup() {

        include( 'class.cpt.php' );

    }

}

new PL_TabKit_Main;
