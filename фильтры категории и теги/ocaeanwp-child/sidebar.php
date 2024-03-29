<?php

/**
 * The sidebar containing the main widget area.
 *
 * @package OceanWP WordPress theme
 */

// Retunr if full width or full screen.
if (in_array(oceanwp_post_layout(), array('full-screen', 'full-width'), true)) {
	return;
} ?>

<?php do_action('ocean_before_sidebar'); ?>

<aside id="right-sidebar" class="sidebar-container widget-area sidebar-primary" <?php oceanwp_schema_markup('sidebar'); ?> role="complementary" aria-label="<?php esc_attr_e('Primary Sidebar', 'oceanwp'); ?>">

	<?php do_action('ocean_before_sidebar_inner'); ?>

	<div id="right-sidebar-inner" class="clr">

		<?php
		$sidebar = oceanwp_get_sidebar();
		if ($sidebar) {
			dynamic_sidebar($sidebar);
		}
		?>

		<?php
		//КАСТОМНЫЙ ХУК ДЛЯ ТОГО ЧТОБЫ ВЫВОДИТЬ ВСЕ ФИЛЬТРЫ В САЙДБАРЕ КАСТОМНЫЙ-ЗНАЧИТ ПИСАЛ Я САМ
		do_action('events_add_filter_sidebar');

		?>

	</div><!-- #sidebar-inner -->
	<?php do_action('events_add_gmap_sidebar'); ?>

	<?php do_action('ocean_after_sidebar_inner'); ?>

</aside><!-- #right-sidebar -->

<?php do_action('ocean_after_sidebar'); ?>