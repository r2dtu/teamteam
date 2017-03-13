<?php

if($_FILES['bg_image']['name']) {

	if(!$_FILES['bg_image']['error']) {
		$valid_file = true;

		if($_FILES['bg_image']['size'] > (1024000 * 10)) {
			$valid_file = false;
			die('Your file\'s size is too large. File size must be <= 10MB');
		}
		if(!getimagesize($_FILES['bg_image']['tmp_name'])) {
			die("Please make sure you are uploading an image.");
		}

		if($valid_file) {

			$username = "dctu@ucsd.edu";
			$targetfolder = "../bg_images/" . $username ."/";

			if (!file_exists($targetfolder)) {
				// THIS DOES NOT WORK ON THE HOST SERVER!!!!!
				mkdir($targetfolder, 0777, true);
			}
			$targetfolder = $targetfolder . basename($_FILES['bg_image']['name']);
			if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $targetfolder)) {
				echo("The file ". basename( $_FILES['bg_image']['name']). " is uploaded");
			}
			else {
				die("Problem uploading file. Please try again later.");
			}
		}
	}	else {
		die('Your upload triggered the following error:  '.$_FILES['bg_image']['error']);
	}
}
?>
