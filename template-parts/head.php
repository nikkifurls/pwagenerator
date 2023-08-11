<head>

<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>

<?php if (!empty($project_data['gtm_id'])): ?>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src='https://www.googletagmanager.com/gtag/js?id=<?php echo $project_data['gtm_id'] ?>'></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', '<?php echo $project_data['gtm_id'] ?>');
	</script>

<?php endif; ?>

<?php if (!empty($project_data['google_ads'])): ?>

	<!-- Google Ads -->
	<script data-ad-client='ca-pub-9469003608080437' async src='https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>

<?php endif; ?>

<?php if (!empty($project_data['fbpixel_id'])): ?>

	<!-- Facebook Pixel Code -->
	<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)}(window, document,'script',
		'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '<?php echo $project_data['fbpixel_id'] ?>');
		fbq('track', 'PageView');
	</script>
	<noscript><img height='1' width='1' style='display:none'
	src='https://www.facebook.com/tr?id=<?php echo $project_data['fbpixel_id'] ?>&ev=PageView&noscript=1'
	/></noscript>
	<!-- End Facebook Pixel Code -->

<?php endif; ?>

<?php if (!empty($project_data['repixel_id'])): ?>

	<!-- Repixel Code -->
	<script>
		(function(w, d, s, id, src){
		w.Repixel = r = {
			init: function(id) {
			w.repixelId = id;
			}
		};
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)){ return; }
		js = d.createElement(s); 
		js.id = id;
		js.async = true;
		js.onload = function(){
			Repixel.init(w.repixelId);
		};
		js.src = src;
		fjs.parentNode.insertBefore(js, fjs);
		}(window, document, 'script', 'repixel', 
		'https://sdk.repixel.co/r.js'));
		Repixel.init('<?php echo $project_data['repixel_id'] ?>');
	</script>
	<!-- Repixel Code -->

<?php endif; ?>

<meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'>

<?php if (!empty($project_data['pages'][$page]['title_seo'])): ?>
	<title><?php echo $project_data['pages'][$page]['title_seo'] ?></title>
<?php endif; ?>

<?php if (!empty($project_data['url']) && !empty($project_data['pages'][$page]['url'])): ?>
	<link rel='canonical' href='https://<?php echo $project_data['url'] ?>/<?php echo $project_data['pages'][$page]['url'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['pages'][$page]['keywords'])): ?>
	<meta name='keywords' content='<?php echo $project_data['pages'][$page]['keywords'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['pages'][$page]['author']) && !empty($project_data['pages'][$page]['author']['name'])): ?>
	<meta name='author' content='<?php echo $project_data['pages'][$page]['author']['name'] ?>'>
<?php endif; ?>

<link rel='apple-touch-icon' sizes='180x180' href='./img/apple-touch-icon.png'>
<link rel='icon' type='image/png' sizes='32x32' href='./img/favicon-32x32.png'>
<link rel='icon' type='image/png' sizes='16x16' href='./img/favicon-16x16.png'>
<link rel='manifest' href='./manifest.json'>
<link rel='mask-icon' href='./img/safari-pinned-tab.svg' color='#000000'>
<link rel='shortcut icon' href='./img/favicon.ico'>
<meta name='msapplication-TileColor' content='#000000'>
<meta name='msapplication-TileImage' content='./img/mstile-144x144.png'>
<meta name='msapplication-config' content='./img/browserconfig.xml'>
<meta name='theme-color' content='#000000'>

<?php if (!empty($project_data['pages'][$page]['description'])): ?>
	<meta name='description' content='<?php echo $project_data['pages'][$page]['description'] ?>'/>
<?php endif; ?>

<meta name='robots' content='index, follow'>
<meta property='og:locale' content='en_US'>
<meta property='og:type' content='website'>

<?php if (!empty($project_data['pages'][$page]['title_seo'])): ?>
	<meta property='og:title' content='<?php echo $project_data['pages'][$page]['title_seo'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['pages'][$page]['description'])): ?>
	<meta property='og:description' content='<?php echo $project_data['pages'][$page]['description'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['url']) && !empty($project_data['pages'][$page]['url'])): ?>
	<meta property='og:url' content='https://<?php echo $project_data['url'] ?>/<?php echo $project_data['pages'][$page]['url'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['title'])): ?>
	<meta property='og:site_name' content='<?php echo $project_data['title'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['social']['facebook'])): ?>
	<meta property='section:publisher' content='https://www.facebook.com/<?php echo $project_data['social']['facebook'] ?>/'>
<?php endif; ?>

<?php if (!empty($project_data['pages'][$page]['image']) && !empty($project_data['pages'][$page]['type']) && ($project_data['pages'][$page]['type'] === 'post')): ?>
	<meta property='og:image' content='https://<?php echo $project_data['url'] ?>/<?php echo $project_data['pages'][$page]['image'] ?>'>
<?php else: ?>
	<?php if (file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/img/share.jpg')): ?>
		<meta property='og:image' content='https://<?php echo $project_data['url'] ?>/img/share.jpg'>
	<?php endif; ?>
<?php endif; ?>

<meta name='twitter:card' content='summary_large_image'>

<?php if (!empty($project_data['pages'][$page]['title_seo'])): ?>
	<meta name='twitter:title' content='<?php echo $project_data['pages'][$page]['title_seo'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['pages'][$page]['description'])): ?>
	<meta name='twitter:description' content='<?php echo $project_data['pages'][$page]['description'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['social']['twitter'])): ?>
	<meta name='twitter:site' content='@<?php echo $project_data['social']['twitter'] ?>'>
<?php endif; ?>

<?php if (!empty($project_data['pages'][$page]['image']) && ($project_data['pages'][$page]['type'] === 'post')): ?>
	<meta name='twitter:image' content='https://<?php echo $project_data['url'] ?>/<?php echo $project_data['pages'][$page]['image'] ?>'>
<?php else: ?>
	<?php if (file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/img/share.jpg')): ?>
		<meta name='twitter:image' content='https://<?php echo $project_data['url'] ?>/img/share.jpg'>
	<?php endif; ?>
<?php endif; ?>

<?php if (!empty($project_data['author']['twitter'])): ?>
	<meta name='twitter:creator' content='@<?php echo $project_data['author']['twitter'] ?>'>
<?php endif; ?>

<?php

echo '<style type="text/css">';

if (!empty($project_data['fonts'])) {

	$heading_font = $project_data['fonts']['heading'] ?? null;
	$heading_font_decoded = strtolower(str_ireplace(' ', '-', $heading_font));
	$body_font = $project_data['fonts']['body'] ?? null;
	$body_font_decoded = strtolower(str_ireplace(' ', '-', $body_font));

	$fonts = [
		[
			$heading_font,
			$heading_font_decoded
		],
		[
			$body_font,
			$body_font_decoded
		],
	];
	$font_weights = [
		'regular',
		'700',
	];

	foreach ($font_weights as $font_weight) {
		foreach ($fonts as $font) {
			$font_url = 'fonts/' . $font[1] . '/' . $font[1] . '-' . $font_weight;
			$font_url_eot = $font_url . '.eot';
			$font_url_woff = $font_url . '.woff';
			$font_url_woff2 = $font_url . '.woff2';
			$font_url_ttf = $font_url . '.ttf';
			$font_url_svg = $font_url . '.svg';
			if (file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/' . $font_url_eot) &&
				file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/' . $font_url_woff) &&
				file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/' . $font_url_woff2) &&
				file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/' . $font_url_ttf) &&
				file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/' . $font_url_svg)) {
				echo "
					@font-face {
						font-family: " . $font[0] . ";
						font-style: normal;
						font-weight: " . $font_weight . ";
						font-display: swap;
						src: url('" . $font_url_eot . "');
						src: local(''),
							 url('" . $font_url_eot . "?#iefix') format('embedded-opentype'),
							 url('" . $font_url_woff2 . "') format('woff2'),
							 url('" . $font_url_woff . "') format('woff'),
							 url('" . $font_url_ttf . "') format('truetype'),
							 url('" . $font_url_svg . "#" . str_ireplace(" ", "-", $font[0]) . "') format('svg');
					}
				";
			}
		}
	}
}

echo '</style>';

?>

<?php if (!empty($project_data['gtm_id'])): ?>
	<link rel='preconnect' href='https://www.googletagmanager.com'>
	<link rel='preconnect' href='https://www.google-analytics.com'>
<?php endif; ?>

<?php if (!empty($project_data['google_ads'])): ?>
	<link rel='preconnect' href='https://tpc.googlesyndication.com'>
<?php endif; ?>

<?php if (!empty($project_data['amazon_ads'])): ?>
	<link rel='preconnect' href='https://fls-na.amazon-adsystem.com'>
<?php endif; ?>

<?php if (!empty($project_data['other_ads'])): ?>
	<link rel='preconnect' href='<?php echo $project_data['other_ads'] ?>'>
<?php endif; ?>

<noscript>
	<p>This web page requires JavaScript to be enabled. JavaScript is an object-oriented computer programming language commonly used to create interactive effects within web browsers.</p>
	<p><a href='https://www.enable-javascript.com'>How do I enable JavaScript?</a></p>
	<p>After enabling JavaScript, refresh this page to continue.</p>
</noscript>

<!-- Set base variables for potential later use. -->
<script type='text/javascript'>

	<?php if (!empty($project_data['url'])) { ?>
		window.baseUrl = "https://<?php echo $project_data['url'] ?>";
	<?php } ?>

	<?php if (!empty($project_data['title'])) { ?>
		window.baseTitle = "<?php echo $project_data['title'] ?>";
	<?php } ?>

	<?php if (!empty($project_data['description'])) { ?>
		window.baseDescription = "<?php echo $project_data['description'] ?>";
	<?php } ?>

</script>

<?php if (!empty($project_data['js_bundle_filename']) && !empty($project_data['files']['cache']) && in_array($project_data['js_bundle_filename'], $project_data['files']['cache'])): ?>
	<script defer src="./<?php echo $project_data['js_bundle_filename'] ?>"></script>
<?php endif; ?>

<?php if (file_exists(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/js/index.js')): ?>
	<!-- Allow for an uncompiled .js file to be included. -->
	<script defer src="./js/index.js"></script>
<?php endif; ?>

<?php if (!empty($project_data['opt_files']) && in_array('opt/google_auth.js', $project_data['opt_files'])) { ?>

	<?php

	$google_api_client_id = (!empty($project_data['google_api']['google_api_client_id']) && $project_data['google_api']['google_api_client_id']) ? $project_data['google_api']['google_api_client_id'] : '';
	$google_api_key = (!empty($project_data['google_api']['google_api_key']) && $project_data['google_api']['google_api_key']) ? $project_data['google_api']['google_api_key'] : '';
	$google_api_scope = (!empty($project_data['google_api']['google_api_scope']) && $project_data['google_api']['google_api_scope']) ? $project_data['google_api']['google_api_scope'] : '';
	$google_api_url = (!empty($project_data['google_api']['google_api_url']) && $project_data['google_api']['google_api_url']) ? $project_data['google_api']['google_api_url'] : '';
	$google_api_callback = (!empty($project_data['google_api']['google_api_callback']) && $project_data['google_api']['google_api_callback']) ? $project_data['google_api']['google_api_callback'] : '';

	?>
	
	<script defer src='./opt/google_auth.js' api_client_id='<?php echo $google_api_client_id ?>' api_key='<?php echo $google_api_key ?>' api_scope='<?php echo $google_api_scope ?>' api_url='<?php echo $google_api_url ?>' api_callback='<?php echo $google_api_callback ?>'></script>

	<script defer src='https://apis.google.com/js/api.js'
      onload='this.onload=function(){};handleClientLoad(<?php echo $google_api_callback ?>)'
	  onreadystatechange="if (this.readyState === 'complete') this.onload()"></script>
<?php } ?>

<?php if (!empty($project_data['fontawesome'])): ?>
	<link rel='stylesheet' href='fonts/fontawesome/css/fontawesome.min.css'>
	<link rel='stylesheet' href='fonts/fontawesome/css/brands.min.css'>
	<link rel='stylesheet' href='fonts/fontawesome/css/solid.min.css'>
<?php endif; ?>

<?php if (!empty($project_data['gtm_id'])): ?>
	<!-- Google Tag Manager (noscript) -->
	<noscript><iframe src='https://www.googletagmanager.com/ns.html?id=<?php echo $project_data['gtm_id'] ?>'
	height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>

<!-- Register service worker -->
<!-- Detects updates to service worker - if detected, displays a notification and reloads the page -->
<script type='text/javascript'>
	if ('serviceWorker' in navigator) {
		window.addEventListener('load', () => {
			navigator.serviceWorker && navigator.serviceWorker.register('./sw.js').then(registration => {
				let refreshing = false;
				navigator.serviceWorker.addEventListener('controllerchange', event => {
					if (!refreshing) {
						showNotification('New verison available! Refreshing...');
						setTimeout(() => {
							window.location.reload();
							refreshing = true;
						}, 1000);
					}
				});
			});
		});
	}
</script>

</head>