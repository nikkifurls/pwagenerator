<div class="container-nav">
	<div class="container">

		<nav>

			<a href="./" class="logo" title="Home"><img alt="logo" src="logo_nav.svg" /></a>

			<?php if (isset($project_data["nav"]) && is_array($project_data["nav"]) && count($project_data["nav"])) { ?>

				<div class="nav">
					<?php foreach ($project_data["nav"] as $index => $nav_page_data) { ?>
						<a href="<?php echo $nav_page_data['url'] ?>">
							<?php echo $nav_page_data['title'] ?>
						</a>
					<?php } ?>
				</div>

			<?php } ?>

			<?php require("social.php"); ?>

		</nav>

	</div>
</div>