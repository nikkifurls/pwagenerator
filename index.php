<?php

$is_admin = true;

require_once("build.php");

?>

<!DOCTYPE html>
<html class="no-js admin" lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin</title>
	<link rel="shortcut icon" href="./favicon.ico">
	<link rel="stylesheet" href="./index.css">
	<script src="./files/common.js"></script>
	<script src="./index.js"></script>
</head>

<body>

	<header>
		<div class="container">
			<div class="text">
				<h1 class="title">Admin</h1>
			</div>
		</div>
	</header>

	<main role="main">
		<div class="container">
			<?php

				// Display contents of projects.json
				$projects_data = get_projects();

				if ($projects_data && is_array($projects_data)) { ?>

					<p><strong><mark>NOTICE: editing is not yet implemented for array fields, change directly in projects.json</mark></strong></p>

					<div class="buttons">
						<button class="build" id="build">Build All</button>
						<button class="deploy" id="deploy">Deploy All</button>
					</div>

					<ul>
						<?php foreach ($projects_data as $data) { ?>
							<li><a href="#<?php echo $data["url"] ?>"><?php echo $data["url"] ?></a></li>
						<?php } ?>
					</ul>

					<form>

					<?php foreach ($projects_data as $data) { ?>

						<fieldset name="<?php echo $data["url"] ?>" id="<?php echo $data["url"] ?>">

							<legend><?php echo $data["url"] ?></legend>

							<div class="buttons">
								<button class="build" id="build">Build</button>
								<button class="deploy" id="deploy">Deploy</button>
							</div>

							<?php foreach ($data as $field => $value) { ?>

								<?php if ($field !== "url") { ?>

									<?php $field_type = gettype($value); ?>
									
									<label for="<?php echo $field ?>" class="<?php echo $field_type ?>">
								
										<span><?php echo $field ?></span>

										<?php if ($field_type == "array") { ?>
											<?php foreach ($value as $subfield => $subvalue) { ?>
												<?php //echo $subfield . ": " . $subvalue ?>
											<?php } ?>
										<?php } else if (($field_type == "string") || ($field_type == null)) { ?>
											<input type="text" id="<?php echo $field ?>" name="<?php echo $field ?>" value="<?php echo $value ?>" />
										<?php } else if ($field_type == "boolean") { ?>
											<input type="checkbox" id="<?php echo $field ?>" name="<?php echo $field ?>" <?php echo $value ? "checked" : "" ?> />
										<?php } ?>

									</label>

								<?php } ?>

							<?php } ?>

						</fieldset>
					<?php } ?>

					</form>

				<?php } else {
					
				}


			?>
		</div>
	</main>

	<div class="notification">
		<div class="container">
			<p></p>
			<span role="img" class="close icon icon-close" title="Close">&#x2716;</span>
		</div>
	</div>

</body>
</html>