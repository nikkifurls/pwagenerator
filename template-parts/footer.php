<?php $is_portfolio = (isset($project_data["social"]["portfolio"]) && isset($project_data["url"]) && ($project_data["url"] !== $project_data["social"]["portfolio"])) ? false : true; ?>

<footer>

	<div class="container">

		<?php if (!$is_portfolio && isset($project_data["author"]) && isset($project_data["author"]["name"]) && isset($project_data["social"]["portfolio"]) && isset($project_data["social"]["paypal"]) && isset($project_data["social"]["patreon"])) { ?>
			<p class="container-text">
				Hi! I'm <a href="https://<?php echo $project_data["social"]["portfolio"] ?>" target="_blank" rel="noopener" title="Portfolio"><?php echo $project_data["author"]["name"] ?> <span class="icon icon-technologist" role="img" title="Technologist">👩‍💻</span></a>. I created <?php echo isset($project_data["title"]) ? $project_data["title"] : "this website" ?>! If you find it useful, please <a href="#" class="share url" target="_blank">share it</a>, and if you can, support me directly on <a href="https://patreon.com/<?php echo $project_data["social"]["patreon"] ?>" target="_blank" rel="noopener" title="Patreon">Patreon</a> or <a href="https://www.paypal.com/<?php echo $project_data["social"]["paypal"] ?>" target="_blank" rel="noopener" title="Paypal">Paypal</a>. Your support is greatly appreciated!&nbsp;<span class="icon icon-heart" role="img" title="Heart">♥</span>
			</p>
		<?php } ?>

		<?php if (isset($project_data["amazon_ads"]) && $project_data["amazon_ads"]) { ?>
			<p class="ad-disclaimer italic">I participate in the Amazon Associates Program, an affiliate advertising program designed to provide a means for websites to earn advertising fees by advertising and linking to Amazon.com. Some links on this website may be affiliate links, and I may get some commission if you buy something or take an action after clicking a link on this website. Of course, I only link to products that I know and love enough to endorse.</p>
		<?php } ?>

		<?php if (isset($project_data["pages"][$page]["image"]) && isset($project_data["pages"][$page]["image_credit"])) { ?>
			<p class="image-credit"><?php echo $project_data["pages"][$page]["image_credit"] ?></p>
		<?php } ?>

		<p class="tech-credit">Built with <a href="https://github.com/nikkifurls/pwagenerator" target="_blank" rel="noopener" title="PWA Generator GitHub">PWA Generator</a></p>

		<?php if (!$is_portfolio) { ?>
			<section class="links">
				<a href="./disclaimer" target="_blank">Disclaimer</a>
				<a href="./privacy-policy" target="_blank">Privacy&nbsp;Policy</a>
				<a href="./terms-and-conditions" target="_blank">Terms&nbsp;and&nbsp;Conditions</a>
			</section>
		<?php } ?>
		
	</div>

</footer>