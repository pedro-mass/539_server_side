<?php
require ("LIB_project1.php");

$page = 1;
$numItems = 5;

if (isset($_GET['page'])) {
	$page = intval($_GET['page']);
}

if (isset($_GET['count'])) {
	$numItems = intval($_GET['count']);
}

$styles = array("css/pedro.css", "css/nav.css");

// create header tags
$output = html_header("News", $styles);

// create banner div
$output .= addBanner();

// create the nav
$output .= addNav();

// add news and page nav
$output .= addNewsContent(false, $page, $numItems, true);

// create footer
$output .= html_footer("");

echo $output;
?>