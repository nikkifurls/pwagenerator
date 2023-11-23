<?php

if (empty($project_data)) {
    return;
}

?>

<div class='container-nav'>
    <div class='container'>
        <nav>
            <?php if (!empty($project_data['nav']['image'])) : ?>
                <a href='./' class='logo' title='Home'>
                    <img alt='logo' src='<?php echo $project_data['nav']['image']; ?>' height='22' width='250'>
                </a>
            <?php endif; ?>
            <?php if (!empty($project_data['nav']['items']) && is_array($project_data['nav']['items'])) { ?>
                <ul class='nav'>
                    <?php foreach ($project_data['nav']['items'] as $index => $nav_page_data) { ?>
                        <li>
                            <a href='<?php echo $nav_page_data['url'] ?>'>
                                <?php echo $nav_page_data['title'] ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
            <?php require('social.php'); ?>
        </nav>
    </div>
</div>