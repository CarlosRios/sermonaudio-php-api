<?php

// Include the API
include_once( 'SermonAudioAPI.php' );

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Sermon Audio API Demo</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="public/js/mediaelement-and-player.min.js"></script>
	<script src="public/js/app.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="public/css/mediaelementplayer.css" />
	<link rel="stylesheet" href="public/css/app.css">
</head>
<body>

	<div class="jumbotron">

		<div class="container">

			<h3>Sermons from SermonAudio.com</h3>

			<p>The following sermons are pulled from SermonAudio using the SermonAudio Json Api</p>

		</div>

	</div>

	<div class="container">

		<section id="search-bar" style="padding: 0px 0px 50px;">

			<?php

			// Get the url for this demo
			$app_url = htmlspecialchars( $_SERVER["PHP_SELF"] );

			?>

			<form action="<?php echo $app_url; ?>" method="POST" style="float:left;">

				<?php

				// Start an instance of the API. Add your key first.
				$sermonAPI = new SermonAudioAPI;
				$sermonAPI->setApiKey( 'EF1D0D28-DBF2-4DFF-AF01-FFC3C7D2BCE0' );

				// A list of speaker names returned from Sermon Audio
				$speakers = $sermonAPI->getSpeakers();

				// The selected speaker. Defaults to the first speaker in the list of speakers if nothing is set.
				$selected_speaker = isset( $_POST['speaker'] ) ? $_POST['speaker'] : $speakers[0];

				// Return if the selected speaker is not found in speakers
				if( ! in_array( $selected_speaker, $speakers ) )
					return;

				if( !empty( $speakers ) ) : ?>
				
				<label for="speaker">Speaker 

					<select name="speaker" id="speaker" class="form-control">

						<?php foreach( $speakers as $speaker ) : ?>

						<?php

						// Set the selected variable
						$selected = '';

						// Apply the selected html attribute to the speaker that matches the selected speaker
						if( $speaker == $selected_speaker )
							$selected = 'selected="selected"'; ?>

						<option value="<?php echo $speaker; ?>" <?php echo $selected; ?>><?php echo $speaker; ?></option>

						<?php endforeach; ?>

					</select>

				</label>

				<?php endif; ?>

				<input type="hidden" name="page" value="1">

				<input type="submit" class="btn btn-primary" name="submit" value="Get Sermons">

			</form>

			<?php

			// The current sermon page. Defaults to page 1 if nothing is set
			$current_sermon_page = isset( $_POST['page'] ) ? abs( $_POST['page'] ) : 1;

			// The number of sermons to request per page. Maximum allowed by Sermon Audio is 100
			$sermons_per_page = 12;

			// The total number of sermons the speaker has published on SermonAudio
			$args = array( 'speaker' => $selected_speaker );
			$total = $sermonAPI->getTotalSermons( $args );

			// The total number of pages to display for the speaker.
			// Value is rounded up to the ceiling value so we don't have
			// any extra digits being displayed 
			$total_pages = ceil( $total / $sermons_per_page );

			?>

			<?php

			/** 
			 * 
			 * Displays a link to load the next page of results if this author has
			 * more sermons to display.
			 * 
			 */
			if( $total_pages > 1 && $current_sermon_page < $total_pages ) : ?>

			<form action="<?php echo $app_url; ?>" method="POST" style="float:right;">

				<input type="hidden" name="speaker" value="<?php echo $selected_speaker; ?>">

				<input type="hidden" name="page" value="<?php echo $current_sermon_page+1; ?>">

				<input id="submit-btn" type="submit" class="btn btn-primary" value="Next Page">

			</form>

			<?php endif; ?>

			<?php

			/** 
			 * 
			 * Displays a link to load the previous page of results if there are
			 * previous results available for the author
			 * 
			 */
			if( $current_sermon_page > 1 ) : ?>

			<form action="<?php echo $app_url; ?>" method="POST" style="float:right;">

				<input type="hidden" name="speaker" value="<?php echo $selected_speaker; ?>">

				<input type="hidden" name="page" value="<?php echo $current_sermon_page-1; ?>">

				<input id="submit-btn" type="submit" class="btn btn-primary" value="Previous Page">

			</form>	

			<?php endif; ?>

			<div style="clear:both"></div>

		</section><!-- end search-bar -->

		<section id="sermons-wrapper">

			<?php

			$args = array(
				'speaker'			=> $selected_speaker,
				'page'				=> $current_sermon_page,
				'sermons_per_page'	=> $sermons_per_page,
				'chunks'			=> 4,
			);

			// A list of sermon objects returned from the Sermon Audio API
			$sermons = $sermonAPI->getSermons( $args );

			// Return if no sermons were found
			if( empty( $sermons ) || ! is_array( $sermons ) )
				return;

			// Run a loop, and display sermon information
			foreach( (array) $sermons as $sermon_set ) : ?>
				
				<div class="row">

					<?php foreach( $sermon_set as $sermon ) : ?>

						<div id="speaker-<?php echo $sermon->speaker ?>" class="sermon-audio-speaker col-md-3">

							<div class="speaker-thumbnail">
								<img src="<?php echo $sermon->speakerThumbURL ?>" alt="<?php echo $sermon->title?>" title="<?php echo $sermon->speaker ?>">
							</div>

							<h3>
								<a href="http://www.sermonaudio.com/sermoninfo.asp?SID=<?php echo $sermon->sermonid ?>" target="_blank" title="<?php echo $sermon->title ?>"><?php echo $sermon->title ?></a>
							</h3>

							<span class="sermon-date"><?php echo $sermon->date ?></span><br>

							<span class="speaker-title"><?php echo $sermon->speaker ?></span><br>

							<span class="speaker-eventtype"><?php echo $sermon->eventtype; ?></span><br>

							<audio src="<?php echo $sermon->mp3URL; ?>"></audio>

							<a href="<?php echo $sermon->mp3URL ?>" class="btn btn-default">Download MP3</a>

						</div>

					<?php endforeach; ?>

				</div>

			<?php endforeach; ?>

		</div><!-- end #sermon-wrapper -->

	</section><!-- end .container -->

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>