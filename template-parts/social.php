<?php

if (empty($project_data)) {
	return;
}

?>

<address class='social'>
	<?php if (!empty($project_data['social']['mailto'])): ?>
		<a href='mailto:<?php echo $project_data['social']['mailto'] ?>' title='Email' class='icon icon-email'><i class='fas fa-envelope'></i></a>
	<?php endif; ?>

	<?php if (!empty($project_data['social']['facebook'])): ?>
		<a href='https://facebook.com/<?php echo $project_data['social']['facebook'] ?>' title='Facebook' class='icon icon-facebook'><i class='fab fa-facebook-f'></i></a>
	<?php endif; ?>

	<?php if (!empty($project_data['social']['twitter'])): ?>
		<a href='https://twitter.com/<?php echo $project_data['social']['twitter'] ?>' title='Twitter' class='icon icon-twitter'><i class='fab fa-twitter'></i></a>
	<?php endif; ?>

	<?php if (!empty($project_data['social']['github'])): ?>
		<a href='https://github.com/<?php echo $project_data['social']['github'] ?>' title='GitHub' class='icon icon-github'><i class='fab fa-github'></i></a>
	<?php endif; ?>

	<?php if (!empty($project_data['social']['paypal'])): ?>
		<a href='https://www.paypal.com/<?php echo $project_data['social']['paypal'] ?>' title='Paypal' class='icon icon-paypal'><i class='fab fa-paypal'></i></a>
	<?php endif; ?>

	<?php if (!empty($project_data['social']['patreon'])): ?>
		<a href='https://patreon.com/<?php echo $project_data['social']['patreon'] ?>' title='Patreon' class='icon icon-patreon'><i class='fab fa-patreon'></i></a>
	<?php endif; ?>

	<?php if (!empty($project_data['social']['yelp'])): ?>
		<a href='https://www.yelp.com/biz/<?php echo $project_data['social']['yelp'] ?>' title='Yelp' class='icon icon-yelp'><i class='fab fa-yelp'></i></a>
	<?php endif; ?>
	
	<?php if (!empty($project_data['social']['tripadvisor'])): ?>
		<a href='https://www.tripadvisor.com/<?php echo $project_data['social']['tripadvisor'] ?>.html' title='Tripadvisor' class='icon icon-tripadvisor'><i class='fab fa-tripadvisor'></i></a>
	<?php endif; ?>
	
	<a href='#' class='share icon icon-share' title='Share'><i class='fas fa-share-alt'></i></a>

	<?php if (!empty($project_data['social']['custom'])) {
		foreach ($project_data['social']['custom'] as $link) { ?>
			<?php if (!empty($link['url'])) { ?>
				<?php echo !empty($link['text']) ? $link['text'] . '&nbsp;&nbsp;' : '' ?><a href='<?php echo $link['url'] ?>' class='custom icon' <?php echo (!empty($link['label']) ? 'title="' . $link["label"] . '"' : '') ?>><?php echo (!empty($link['link']) ? $link['link'] : '') ?></a>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	</address>