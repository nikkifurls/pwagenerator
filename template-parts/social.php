<?php if (isset($project_data["social"]) && is_array($project_data["social"]) && count($project_data["social"])) { ?>

	<address class="social">
		<?php if (isset($project_data["social"]["mailto"])) { ?>
			<a href="mailto:<?php echo $project_data["social"]["mailto"] ?>" target="_blank" rel="noopener" title="Email" class="icon icon-email"><i class="fas fa-envelope"></i></a>
		<?php } ?>

		<?php if (isset($project_data["social"]["facebook"])) { ?>
			<a href="https://facebook.com/<?php echo $project_data["social"]["facebook"] ?>" target="_blank" rel="noopener" title="Facebook" class="icon icon-facebook"><i class="fab fa-facebook-f"></i></a>
		<?php } ?>

		<?php if (isset($project_data["social"]["twitter"])) { ?>
			<a href="https://twitter.com/<?php echo $project_data["social"]["twitter"] ?>" target="_blank" rel="noopener" title="Twitter" class="icon icon-twitter"><i class="fab fa-twitter"></i></a>
		<?php } ?>

		<?php if (isset($project_data["social"]["github"])) { ?>
			<a href="https://github.com/<?php echo $project_data["social"]["github"] ?>" target="_blank" rel="noopener" title="GitHub" class="icon icon-github"><i class="fab fa-github"></i></a>
		<?php } ?>

		<?php if (isset($project_data["social"]["paypal"])) { ?>
			<a href="https://www.paypal.com/<?php echo $project_data["social"]["paypal"] ?>" target="_blank" rel="noopener" title="Paypal" class="icon icon-paypal"><i class="fab fa-paypal"></i></a>
		<?php } ?>

		<?php if (isset($project_data["social"]["patreon"])) { ?>
			<a href="https://patreon.com/<?php echo $project_data["social"]["patreon"] ?>" target="_blank" rel="noopener" title="Patreon" class="icon icon-patreon"><i class="fab fa-patreon"></i></a>
		<?php } ?>

		<?php if (isset($project_data["social"]["yelp"]) && $project_data["social"]["yelp"]) { ?>
			<a href="https://www.yelp.com/biz/<?php echo $project_data["social"]["yelp"] ?>" target="_blank" rel="noopener" title="Yelp" class="icon icon-yelp"><i class="fab fa-yelp"></i></a>
		<?php } ?>
		
		<?php if (isset($project_data["social"]["tripadvisor"]) && $project_data["social"]["tripadvisor"]) { ?>
			<a href="https://www.tripadvisor.com/<?php echo $project_data["social"]["tripadvisor"] ?>.html" target="_blank" rel="noopener" title="Tripadvisor" class="icon icon-tripadvisor"><i class="fab fa-tripadvisor"></i></a>
		<?php } ?>
		
		<a href="#" class="share icon icon-share" target="_blank" rel="noopener" title="Share"><i class="fas fa-share-alt"></i></a>

		<?php if (isset($project_data["social"]["custom"])) {
			foreach ($project_data["social"]["custom"] as $link) { ?>
				<?php if (isset($link["url"])) { ?>
					<?php echo isset($link["text"]) ? $link["text"] . "&nbsp;&nbsp;" : "" ?><a href="<?php echo $link['url'] ?>" target="_blank" class="custom icon" rel="noopener" <?php echo (isset($link["label"]) ? "title='" . $link["label"] . "'" : "") ?>><?php echo (isset($link["link"]) ? $link["link"] : "") ?></a>
				<?php } ?>
			<?php } ?>
		<?php } ?>
	</address>

<?php } ?>