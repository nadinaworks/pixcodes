<?php

if (!defined('ABSPATH')) die('-1');

class WpGradeShortcode_RestaurantMenu extends  WpGradeShortcode {

    public function __construct($settings = array()) {

        $this->self_closed = false;
        $this->direct = false;
        $this->name = "Restaurant Menu";
        $this->code = "restaurantmenu";
        $this->icon = "icon-list-alt";

        $this->params = array(
	        'restaurantmenu_info' => array(
		        'type' => 'info',
		        'value' => '<p>We have devised the menu system to be as flexible and straight forward as possible. We rely on <strong>a couple of markers</strong> to identify the <strong>3 sections</strong> of each menu product.</p>
		        <p>First there is the <strong>title marker</strong>: <strong>##</strong>Product Title Here</p>
		        <p>After you should add the <strong>description</strong>: <strong>**</strong>Product Description Here</p>
		        <p>Last you should add the <strong>price</strong>: <strong>==</strong>Product Price Here</p>
		        <p>The only one that is <strong>necessary</strong> to keep the system running is the <strong>Title</strong>, <strong>the Description and Price are optional</strong>. At the same time you can have <strong>multiple</strong> Description and Price groups in case you have <strong>subproducts</strong>.</p>
		        <p>Here is a sample text to get you started (simply hit insert and we will add it for you):</p>',
		        'admin_class' => 'span10 push1'
	        ),
	        'content_text' => array(
		        'type' => 'textarea',
		        'name' => '',
		        'admin_class' => 'span10 push1',
		        'is_content' => true,
		        'rows' => 5,
		        'predefined' =>'#Section Title
-----

##First Product Title
**Description of the first product
==$15

##Second Product Title
**Description of the second product (no price)

##Third Product Title
==$23.99',
	        ),
        );

	    // allow the theme or other plugins to "hook" into this shortcode's params
	    $this->params = apply_filters('pixcodes_filter_params_for_' . strtolower($this->name), $this->params);

        add_shortcode('restaurantmenu', array( $this, 'add_restaurantmenu_shortcode') );

    }

    public function add_restaurantmenu_shortcode( $atts, $content ) {
         extract( shortcode_atts( array(
             //no params here
         ), $atts ) );

	    /**
	     * Template localization between plugin and theme
	     */
	    $located = locate_template("templates/shortcodes/{$this->code}.php", false, false);
	    if(!$located) {
		    $located = dirname(__FILE__).'/templates/'.$this->code.'.php';
	    }

	    // load it
	    ob_start();
	    require $located;
	    return ob_get_clean();
    }
}