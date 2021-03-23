<header <?php echo 'class="image-' . $project_data['pages'][$page]['image_type'] . '"'; echo ($project_data['pages'][$page]['image_type'] == 'background') ? 'style="background-image:url(' . $project_data['pages'][$page]['image'] . ')"' : '' ?>>

	<div class="container">

		<?php if ($project_data["pages"][$page]["image_type"] == "logo") { ?>
			<div class="logo">
				<img alt="Logo" src="<?php echo $project_data["pages"][$page]["image"] ?>">
			</div>
		<?php } ?>
		
		<div class="text">

			<?php if (($project_data["pages"][$page]["type"] == "index") && isset($project_data["header"]["title"]) && isset($project_data["header"]["description"])) { ?>

				<hgroup>
					<h1 class="title"><?php echo $project_data["header"]["title"] ?></h1>
					<p class="description"><?php echo $project_data["header"]["description"] ?></p>
				</hgroup>

			<?php } else if (($project_data["pages"][$page]["type"] == "index") || ($project_data["pages"][$page]["type"] == "search") || ($project_data["pages"][$page]["type"] == "special")) { ?>

				<hgroup>
					<h1 class="title"><?php echo $project_data["title"] ?></h1>
					<p class="description"><?php echo $project_data["description"] ?></p>
				</hgroup>

			<?php } else { ?>

				<hgroup>
					<h1 class="title"><?php echo $project_data["pages"][$page]["title"] ?></h1>
					<p class="description"><?php echo $project_data["pages"][$page]["description"] ?></p>
				</hgroup>

			<?php } ?>

			<?php if ($project_data["pages"][$page]["type"] == "post") { ?>

				<?php if (isset($project_data["pages"][$page]["author"]) && isset($project_data["pages"][$page]["author"]["name"]) && $project_data["pages"][$page]["author"]["name"]) { ?>
					<address class="author"><?php echo $project_data["pages"][$page]["author"]["name"] ?></address>
				<?php } ?>

				<?php if (isset($project_data["pages"][$page]["date"]) && $project_data["pages"][$page]["date"]) { ?>
					<?php if (isset($project_data["pages"][$page]["date_full"]) && $project_data["pages"][$page]["date_full"]) { ?>
						<time pubdate datetime="<?php echo $project_data["pages"][$page]["date"] ?>" title="<?php echo $project_data["pages"][$page]["date_full"] ?>"><?php echo $project_data["pages"][$page]["date_full"] ?></time>
					<?php } else { ?>
						<time pubdate datetime="<?php echo $project_data["pages"][$page]["date"] ?>" title="<?php echo $project_data["pages"][$page]["date"] ?>"><?php echo $project_data["pages"][$page]["date"] ?></time>
					<?php } ?>
				<?php } ?>

			<?php } ?>

		</div>
		
	</div>

</header>