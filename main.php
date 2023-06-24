<?php 

require_once("build.php");

$project = null;
$page = null;

foreach ($argv as $index => $arg) {
	if (stripos($arg, "main.php") !== false) {
		// Do nothing
	} else if (stripos($arg, ".") !== false) {
		$project = $arg;
	} else {
		$page = $arg;
	}
}

if ($page) {
	$project_data = get_project_data($project, $page);
}

if ($project) { ?>

	<!DOCTYPE html>
	<html class="no-js" lang="en">

	<?php require("template-parts/head.php"); ?>

	<body class="<?php echo ($page == "index") ? $page : $project_data["pages"][$page]["type"] . " " . $project_data["pages"][$page]["type"] . "-" . $page ?>">

		<?php require("template-parts/nav.php"); ?>
		<?php require("template-parts/header.php"); ?>

		<main role="main">
			<div class="container">

				<?php if ($project_data["pages"][$page]["type"] == "template") { ?>

					<section class="container-text">
						<?php require("templates/" . $page . ".php"); ?>
					</section>

				<?php } else if ($project_data["pages"][$page]["type"] == "post") { ?>

					<section class="container-text">
						<?php echo $project_data["pages"][$page]["content"]; ?>
					</section>
				
				<?php } else { ?>
					<?php require(dirname(__DIR__) . "/" . $project_data["url"] . "/main.php"); ?>
				<?php } ?>
				
				<?php if (($project_data["pages"][$page]["type"] != "template") && isset($project_data["links"]) && sizeof($project_data["links"])) { ?>

					<section class="links list">
						<h2>Links</h2>
						<ul>
							<?php foreach ($project_data["links"] as $link => $link_data) { ?>
								<li><a href="<?php echo $link_data["url"] ?>" target="_blank" rel="noopener"><?php echo $link_data["title"] ?></a></li>
							<?php } ?>
						</ul>
					</section>

				<?php } ?>

			</div>
		</main>

		<?php require("template-parts/footer.php"); ?>
		<?php require("template-parts/nav.php"); ?>

		<div class="notification">
			<div class="container">
				<p></p>
				<span role="img" class="close icon icon-close" title="Close">✖️</span>
			</div>
		</div>

	</body>
	</html>

<?php } ?>