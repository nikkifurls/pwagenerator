<?php

require_once('inc/class-build.php');

if (empty($argv[1]) || empty($argv[2])) {
	exit;
}

$page = $argv[1];
$project_data = json_decode($argv[2], true);

if (empty($project_data) || !is_array($project_data)) {
	exit;
}

?>

<!DOCTYPE html>
<html class='no-js' lang='en'>
	<?php require('template-parts/head.php'); ?>
	<body class='<?php echo ($page === 'index') ? $page : $project_data['pages'][$page]['type'] . ' ' . $project_data['pages'][$page]['type'] . '-' . $page ?>'>
		<?php require('template-parts/nav.php'); ?>
		<?php require('template-parts/header.php'); ?>
		<main role='main'>
			<div class='container'>
				<?php if ($project_data['pages'][$page]['type'] === 'template'): ?>
					<section class='container-text'>
						<?php require('templates/' . $page . '.php'); ?>
					</section>
				<?php elseif ($project_data['pages'][$page]['type'] === 'post'): ?>
					<section class='container-text'>
						<?php echo $project_data['pages'][$page]['content']; ?>
					</section>
				<?php else: ?>
					<?php require(dirname(__DIR__, 2) . '/' . $project_data['url'] . '/index.php'); ?>
				<?php endif; ?>
				
				<?php if (($project_data['pages'][$page]['type'] != 'template') && !empty($project_data['links']) && count($project_data['links'])): ?>
					<section class='links list'>
						<h2>Links</h2>
						<ul>
							<?php foreach ($project_data['links'] as $link => $link_data): ?>
								<li>
									<a href='<?php echo $link_data['url'] ?>'><?php echo $link_data['title'] ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
			</div>
		</main>
		<?php require('template-parts/footer.php'); ?>
		<?php require('template-parts/nav.php'); ?>
		<dialog class="notification">
			<div class='container'>
				<p></p>
				<span role='img' class='close icon icon-close' title='Close'><i class='fas fa-times'></i></span>
			</div>
		</dialog>
	</body>
</html> 