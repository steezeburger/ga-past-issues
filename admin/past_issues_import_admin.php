<?php
$_POST = array_map('stripslashes_deep', $_POST);
// Config file location
$config_file_loc = plugin_dir_path(__FILE__) . 'configuration.json';

function generate_html($config_file) {
	// Check if config file exists
	if (file_exists($config_file)) :
		// Get file contents as string and decode json into array of objects
		$issues = json_decode(file_get_contents($config_file));
		$issue_count = count(get_object_vars($issues));
		//Generate proper HTML
		?>

		<form class = "column" name="gazette1_form" method="post"
		      action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="gazette1_hidden" value="Y<?php echo $issue_count; ?>">
			<p class="submit">
				<input type="submit" name="gazette1_submit"
				       value="<?php _e('Update', 'gazette1_trdom' ) ?>" />
			</p>

			<?php
			$count = 0;
			foreach($issues as $issue): ?>
				<div class="portlet">
					<div class="portlet-header"><?php echo $issue->title; ?></div>
					<div class="portlet-content">
						<p>
							<?php _e( "Date: " ); ?>
							<input type="text" name="video[vid<?php echo $count;?>][title]"
							       value="<?php echo $issue->title; ?>" size="50" placeholder="ex: 4/8/2015">
						</p>
						<p>
							<?php _e( "Cover URL: " ); ?>
							<input type="text" name="video[vid<?php echo $count;?>][iconURL]"
							       value="<?php echo $issue->iconURL; ?>" size="50" placeholder="ex: http://okgazette.com/wp-content/uploads/picture.jpg">
						</p>
						<p>
							<?php _e( "Issuu URL: " ); ?>
							<input type="text" name="video[vid<?php echo $count;?>][issuuURL]"
							       value="<?php echo $issue->issuuURL; ?>" size="50" placeholder="ex: http://issuu.com/okgazette/docs/okgazette_4-8-15lr?e=11698495/12232508">
						</p>
					</div>
				</div>
				<?php $count++; ?>
			<?php endforeach; ?>

		</form>

	<?php endif;
} ?>

<body>
<div class="wrap">
	<?php echo "<h2>" . __( 'Past Issues Gallery Options', 'gazette1_trdom' ) . "</h2>"; ?>
	<hr>
	<ul>
		<li><p>Images for the cover should ideally be 512 x 680.</p></li>
		<li><p>The date should be of format MM/DD/YYYY or M/D/YYYY (ex. 4/10/2014 or 12/1/2015).</p></li>
		<li><p>Order issues by clicking, dragging, and dropping the tiles below. The most recent issue will display predominantly. </p></li>
		<li><p>Edit the entries by clicking the Plus Sign icon and changing the values in the input fields and clicking Update.</p></li>
		<li><p>Delete an entry by clicking the Trash icon to the right of the entry title.</p></li>
		<li>
			<ul><p class="bold-it"><strong>Usage:</strong></p>
				<p>This plugin provides the shortcode <span class="bold-it">[past-issues]</span>.
					   You can put this on any page or post you want. If no year is specified,
					   the current year's issues will be displayed.</p>
				<p>You can also use <span class="bold-it">[past-issues year=XXXX]</span>.
					   If there are issues for that year, they will be displayed.</p>
			</ul>
		</li>
	</ul>

	<p class="bold-it"><strong>NOTE: Changes will not persist unless you click Update, even when deleting items.</strong></p>

	<button id="create-entry" type="button">Add New</button>

	<?php
	// User submits form
	if($_POST['gazette1_hidden'][0] == 'Y') {
		// Get video count for looping
		$issue_count = (int)$_POST['gazette1_hidden'][1];
		// Init empty array
		$arr = array();
		// For number of videos, store proper key-value pairs for current video in loop
		$arr = ($_POST['video']);
		$contents = json_encode($arr);
		// Open and write to file. Erases the contents of the file or creates
		// a new file if it doesn't exist. File pointer starts at the beginning
		$file_handler = fopen($config_file_loc, "w");
		fwrite($file_handler, $contents);
		fclose($file_handler);
	}
	// Generate HTML for page
	generate_html($config_file_loc);

	?>
</div>
</body>