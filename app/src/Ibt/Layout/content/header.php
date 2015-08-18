<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>I'll be there in</title>
		<meta name="description" content="I'll be there in" />
		<meta name="keywords" content="I'll be there in, travel, countdown, countdown timer, traveling, events, music, places, locations, your time matters"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<script type="text/javascript">
			if (typeof Function.bind === "undefined") {
				window.location.href = '/browser/';
			}
		</script>

		<?php \Ibt\Events::fire( 'header' ); ?>

		<link rel="icon" href="<?php echo __assets__; ?>img/i.ico">
		<link href='//fonts.googleapis.com/css?family=Open Sans:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="<?php echo __assets__; ?>dist/app.min.css" />
	</head>
	<body>
		<div class="js-page-ldr page-ldr"></div>
		<div id="header-wrap" class="js-header-wrap">
			<div id="header">
				<div class="head-title">
					<a href="<?php echo __host__; ?>" class="home-link fl js-page">my travel countdown!</a>
					<div class="fr menu" id="js-user"></div>
					<div class="fr menu" id="js-menu">
						<a href="<?php echo __host__; ?>page/travel/" class="js-page uc">Travel</a>
						<a href="<?php echo __host__; ?>page/latest/" class="js-page uc">Latest</a>
					</div>
				</div>
			</div>
		</div>
		<div id="content-wrap" class="js-content-wrap">
			<span class="top-right"></span>
			<span class="bottom-left"></span>
			<div id="wrap" class="js-wrap">