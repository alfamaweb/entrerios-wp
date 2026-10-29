<?php
/**
 * Post único do blog. Molde copiado pelo scaffolding para single-{cpt}.php.
 *
 * @package AlfamaWeb
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) :
	the_post();
	?>
	<main id="conteudo">
		<article class="single-post py-12 desktop:py-20">
			<div class="container">
				<div class="grid grid-cols-12">
					<div class="col-span-12 desktop:col-span-8">
						<nav class="aw-breadcrumb" aria-label="<?php esc_attr_e('Você está aqui', 'alfama-web'); ?>">
							<a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Início', 'alfama-web'); ?></a>
							<span aria-hidden="true">/</span>
							<span><?php the_title(); ?></span>
						</nav>

						<h1 class="mt-4 text-3xl font-bold desktop:text-4xl"><?php the_title(); ?></h1>

						<p class="aw-meta mt-3">
							<time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
							<?php
							$cats = get_the_category();
							if ($cats) :
								?>
								<span aria-hidden="true">·</span>
								<span><?php echo esc_html($cats[0]->name); ?></span>
							<?php endif; ?>
						</p>

						<?php if (has_post_thumbnail()) : ?>
							<figure class="mt-8">
								<?php the_post_thumbnail('aw-hero', array('class' => 'w-full rounded-lg object-cover', 'loading' => 'eager')); ?>
							</figure>
						<?php endif; ?>

						<div class="aw-prose mt-8">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
