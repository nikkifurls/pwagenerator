<?php $is_portfolio = empty($project_data['social']['portfolio']) && empty($project_data['url']) && $project_data['url'] !== $project_data['social']['portfolio']; ?>

<footer>
	<div class='container'>
		<?php if (
			empty($is_portfolio)
			&& !empty($project_data['author'])
			&& !empty($project_data['author']['name'])
			&& !empty($project_data['social']['portfolio'])
		): ?>
			<p class='container-text'>
				Hi! I'm <a href='https://<?php echo $project_data['social']['portfolio'] ?>' title='Portfolio'><?php echo $project_data['author']['name'] ?> <span class='icon icon-technologist' role='img' title='Technologist'>👩‍💻</span></a>. I created <?php echo !empty($project_data['title']) ? $project_data['title'] : 'this website' ?>! If you find it useful, please <a href='#' class='share url'>share it</a>.

				<?php if (!empty($project_data['social']['paypal']) || !empty($project_data['social']['patreon'])): ?>
					Support me directly on 
					<?php if (!empty($project_data['social']['patreon'])): ?>
						<a href='https://patreon.com/<?php echo $project_data['social']['patreon'] ?>' title='Patreon'>Patreon</a>
					<?php endif; ?>
					<?php if (!empty($project_data['social']['paypal']) && !empty($project_data['social']['patreon'])): ?>
						or
					<?php endif; ?>
					<?php if (!empty($project_data['social']['paypal'])): ?>
						<a href='https://www.paypal.com/<?php echo $project_data['social']['paypal'] ?>' title='Paypal'>Paypal</a>.
					<?php endif; ?>
					Your support is greatly appreciated!&nbsp;<span class='icon icon-heart' role='img' title='Heart'>♥</span>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<?php if (!empty($project_data['amazon_ads'])): ?>
			<i class='ad-disclaimer'>I participate in the Amazon Associates Program, an affiliate advertising program designed to provide a means for websites to earn advertising fees by advertising and linking to Amazon.com. Some links on this website may be affiliate links, and I may get some commission if you buy something or take an action after clicking a link on this website. Of course, I only link to products that I know and love enough to endorse.</i>
		<?php endif; ?>

		<?php if (!empty($project_data['pages'][$page]['image']) && !empty($project_data['pages'][$page]['image_credit'])): ?>
			<p class='image-credit'><?php echo $project_data['pages'][$page]['image_credit'] ?></p>
		<?php endif; ?>

		<p class='tech-credit'>Built with <a href='https://github.com/nikkifurls/pwagenerator' title='PWA Generator GitHub'>PWA Generator</a></p>

		<?php if (empty($is_portfolio)): ?>
			<section class='links'>
				<a href='./disclaimer'>Disclaimer</a>
				<a href='./privacy-policy'>Privacy&nbsp;Policy</a>
				<a href='./terms-and-conditions'>Terms&nbsp;and&nbsp;Conditions</a>
			</section>
		<?php endif; ?>
	</div>
</footer>