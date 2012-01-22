<?php
require("LIB_project1.php");

$offset=0;
$numItems = 5;

if(isset($_GET['page'])){
	if(($result = intval($_GET['page']))>0){
		$offset = ($result -1)*$numItems;
	}
}

$styles = array("css/pedro.css", "css/nav.css");

// create header tags
$output = html_header("Pedro News - News", $styles);

// create banner div
$output .= addBanner();

// create the nav
$output .= addNav();

// add news and editorial
$output .= addContent(false, $offset, $numItems);

// add page navigation
$output .= addPageNav();

// create footer
$output .= html_footer("");

echo $output;
?>