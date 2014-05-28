<?php
//initialize things
$output = '';
define( 'SECTION_MARKER', '#' );
define( 'TITLE_MARKER', '##' );
define( 'DESCRIPTION_MARKER', '**' );
define( 'PRICE_MARKER', '==' );


/* Lets get to parsing the hell out of the received content so we can have something to eat */

//fist make sure no loose ends or beginnings
$menu = trim( $this->get_clean_content( $content ) );

//remove <p> - we just need the </p>s to split by
$menu = str_replace( "<p>", "", $menu );

/**
 * now split it by paragraphs - this is for us the empty line
 * WordPress's autop adds paragraphs only when encountering empty lines
 * and since we treat empty lines like a show stopper - new product
 * this is good enough for us - no need to un-autop and split by new line chars
 */
$lines = preg_split( '#(<\/p>|<br \/>|<br\/>|<br>)#', $menu );
//remove any empty array elements - empty strings
$lines = array_filter( $lines, 'strlen' );

//open the wrapper and let the show begin
$output .= '<div class="menu-list">' . PHP_EOL;

//remember if we have outputted the open tag
$opened_list = false;
$opened_product = false;
$opened_description = false;
$number_of_descriptions = 0;

//first lets clean the lines of empty characters
foreach ( $lines as $key => $line ) {
	$lines[$key] = trim($line);
}

//now go through each line and give it the appropriate markup
foreach ( $lines as $key => $line ) {
	/*
	 * Now for the real hardwork
	 * Go through each line and see its beginning to know how to treat it
	 * The ----- is a special case as it has nothing else
	 */
	if ( $line == '---' || $line == '----' || $line == '-----' ) {
		$output .= '<hr class="separator"/>' . PHP_EOL;

		continue;
	}

	/*
	 * Now to test for the front markers - from complex to simple
	 */

	//Product Title
	if ( 0 === strpos( $line, TITLE_MARKER ) ) {
		//since we have found a product we need to make sure that the product list is started
		if ( false === $opened_list ) {
			$output .= '<ul class="menu-list__items">' . PHP_EOL;
			$opened_list = true;
		}

		//close any previously opened products
		if (true === $opened_product) {
			$output .= '</li>' . PHP_EOL;
			$opened_product = false;
		}
		//we have a new product so we better open a new wrapper
		$output .= '<li class="menu-list__item">' . PHP_EOL;
		$opened_product = true;

		//now output the title without the first 2 characters
		$output .= '<h4 class="menu-list__item-title">' .substr($line,2). '</h4>' . PHP_EOL;

		// we need to do some look-ahead to see if we have a product with subproducts - multiple description-price groups
		$number_of_descriptions = 0;
		$idx = $key + 1;
		while ($idx < count($lines) && 0 !== strpos( $lines[$idx], TITLE_MARKER )) {
			if ( 0 === strpos( $lines[$idx], DESCRIPTION_MARKER ) ) {
				$number_of_descriptions++;
			}

			$idx++;
		}

		continue;
	}

	//Product description
	if ( 0 === strpos( $line, DESCRIPTION_MARKER ) ) {
		//first close any opened description
		if (true === $opened_description) {
			$output .= '</p>' . PHP_EOL;
			$opened_description = false;
		}
		//output the description without the first 2 characters
		$output .= '<p class="menu-list__item-desc">' .substr($line,2);
		$opened_description = true;

		if ($number_of_descriptions < 2) {
			//we can safely close the description paragraph as the price will align with the product title not the description
			$output .= '</p>' . PHP_EOL;
			$opened_description = false;
		}

		continue;
	}

	//Product price
	if ( 0 === strpos( $line, PRICE_MARKER ) ) {
		//output the price without the first 2 characters
		$output .= '<span class="menu-list__item-price">' .substr($line,2). '</span>';
		//close any opened description
		if (true === $opened_description) {
			$output .= '</p>' . PHP_EOL;
			$opened_description = false;
		}

		continue;
	}

	//Section Title
	if ( 0 === strpos( $line, SECTION_MARKER ) ) {
		//first we need to know if there are any lists, products or descriptions opened and close them
		if (true === $opened_description) {
			$output .= '</p>' . PHP_EOL;
			$opened_description = false;
		}

		//close any previously opened products
		if (true === $opened_product) {
			$output .= '</li>' . PHP_EOL;
			$opened_product = false;
		}

		if (true === $opened_list) {
			$output .= '</ul>' . PHP_EOL;
			$opened_list = false;
		}

		//now output the section title without the first character
		$output .= '<h2 class="menu-list__title">' .substr($line,1). '</h2>' . PHP_EOL;

		continue;
	}
}

//some last sanity check - no loose ends
//close any previously opened descriptions
if (true === $opened_description) {
	$output .= '</p>' . PHP_EOL;
	$opened_description = false;
}

//close any previously opened products
if (true === $opened_product) {
	$output .= '</li>' . PHP_EOL;
	$opened_product = false;
}

if (true === $opened_list) {
	$output .= '</ul>' . PHP_EOL;
	$opened_list = false;
}

//all done - close the wrapper
$output .= '</div>' . PHP_EOL;

echo $output;