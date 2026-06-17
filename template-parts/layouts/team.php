<?php
/**
 * Layout: Team / Behandler (portrait cards)
 *
 * Source des membres : CPT `team` (groupé par taxonomie `funktion`,
 * tri manuel via menu_order). Fallback : ancien repeater ACF `members`
 * tant que le CPT est vide.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'tm_eyebrow' );
$title   = get_sub_field( 'tm_title' );

if ( ! function_exists( 'kc_team_card' ) ) :
	/**
	 * Render one team member card (CPT post).
	 *
	 * @param WP_Post $member Team post.
	 */
	function kc_team_card( $member ) {
		$photo_id = get_post_thumbnail_id( $member );
		?>
	<article class="team-card">
		<?php if ( $photo_id ) : ?>
		<div class="team-card__img">
			<?php
			echo wp_get_attachment_image(
				$photo_id,
				'medium',
				false,
				[
					'loading' => 'lazy',
					'alt'     => get_the_title( $member ),
				]
			);
			?>
		</div>
		<?php endif; ?>
		<div class="team-card__body">
			<h3><?php echo kc_team_name_html( get_the_title( $member ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Tokens in kc_team_name_html() einzeln escaped ?></h3>
			<p class="team-card__role"><?php echo esc_html( get_field( 'tm_role', $member ) ); ?></p>
			<?php
			$bio = get_field( 'tm_bio', $member );
			if ( $bio ) :
				?>
				<p><?php echo esc_html( $bio ); ?></p><?php endif; ?>
		</div>
	</article>
		<?php
	}
endif;

$team_posts = get_posts(
	[
		'post_type'      => 'team',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	]
);
?>
<section class="section-team reveal" id="team">
	<div class="container">
		<?php
		if ( $eyebrow ) :
			?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
		<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $team_posts ) : ?>
			<?php
			$grouped   = [];
			$ungrouped = [];
			foreach ( $team_posts as $member ) {
				// Un membre n'apparait que dans UN groupe : son premier terme funktion.
				$terms = get_the_terms( $member, 'funktion' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$grouped[ $terms[0]->term_id ]['term']      = $terms[0];
					$grouped[ $terms[0]->term_id ]['members'][] = $member;
				} else {
					$ungrouped[] = $member;
				}
			}
			?>
			<?php foreach ( $grouped as $group ) : ?>
			<h3 class="team-group__title"><?php echo esc_html( $group['term']->name ); ?></h3>
			<div class="team-grid">
				<?php
				foreach ( $group['members'] as $member ) {
					kc_team_card( $member );
				}
				?>
			</div>
			<?php endforeach; ?>
			<?php if ( $ungrouped ) : ?>
			<div class="team-grid">
				<?php
				foreach ( $ungrouped as $member ) {
					kc_team_card( $member );
				}
				?>
			</div>
			<?php endif; ?>
		<?php else : ?>
			<?php
			// Fallback : ancien repeater ACF (avant migration vers le CPT).
			$members = get_sub_field( 'members' );
			if ( $members ) :
				?>
			<div class="team-grid">
				<?php
				foreach ( $members as $member ) :
					$photo = $member['photo'];
					?>
				<article class="team-card">
					<?php if ( $photo ) : ?>
					<div class="team-card__img">
						<img src="<?php echo esc_url( $photo['sizes']['medium'] ?? $photo['url'] ); ?>"
							alt="<?php echo esc_attr( $photo['alt'] ?: $member['tm_name'] ); ?>"
							width="<?php echo absint( $photo['sizes']['medium-width'] ?? $photo['width'] ); ?>"
							height="<?php echo absint( $photo['sizes']['medium-height'] ?? $photo['height'] ); ?>"
							loading="lazy">
					</div>
					<?php endif; ?>
					<div class="team-card__body">
						<h3><?php echo kc_team_name_html( $member['tm_name'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Tokens in kc_team_name_html() einzeln escaped ?></h3>
						<p class="team-card__role"><?php echo esc_html( $member['tm_role'] ); ?></p>
						<?php
						if ( $member['tm_bio'] ) :
							?>
							<p><?php echo esc_html( $member['tm_bio'] ); ?></p><?php endif; ?>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
