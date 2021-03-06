<?php
	// init the associative array array
  $polls = array();
	
	// get contents of a file
	// test if the file exists
	$filename = 'poll_data.txt';
	$delim = "|";
	if(file_exists($filename) && is_readable($filename)){
		$file_lines = file($filename);
		
		foreach ($file_lines as $line) {
			// break into 2 pieces
			list($topic,$question) = explode($delim,$line);
			
			// add to the array
			$polls[$topic] = $question;
		}
	}
	
	$poll_page = "take_a_poll.php";
	$add_poll_page = "add_poll.php";
	
?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<title>Choose a Poll</title>
		<meta name="description" content="" />
		<meta name="author" content="tuxedo" />
		<meta name="viewport" content="width=device-width; initial-scale=1.0" />
		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico" />
		<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
	</head>
	<body>
		<h1>Choose a Poll</h1>
		
		<ul>
			<?php
				$result = "";
				foreach ($polls as $topic => $desc) {
					// start the list item
					echo "<li>\n";
					// setup the link
					echo "<a href='$poll_page?cat=".urlencode($topic)."'>$topic</a>";
					// setup the link - description separator
					echo " - ";
					// setup the description
					echo $desc;
					// end the list item
					echo "</li>\n";
				}
			?>
		</ul>
		
		<h3><a href='<?php echo $add_poll_page?>' >Add a Poll</a></h3>
	</body>
</html>
