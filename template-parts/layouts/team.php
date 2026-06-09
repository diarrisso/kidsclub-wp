<?php
/**
 * Layout: Team / Behandler (portrait cards)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$eyebrow = get_sub_field( 'tm_eyebrow' );
$title   = get_sub_field( 'tm_title' );
$members = get_sub_field( 'members' );
?>
<section class="section-team reveal" id="team">
    <div class="container">
        <?php if ( $eyebrow ) : ?><span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
        <?php if ( $members ) : ?>
        <div class="team-grid">
            <?php foreach ( $members as $member ) :
                $photo = $member['photo'];
            ?>
            <article class="team-card">
                <?php if ( $photo ) : ?>
                <div class="team-card__img">
                    <img src="<?php echo esc_url( $photo['sizes']['medium'] ?? $photo['url'] ); ?>"
                         alt="<?php echo esc_attr( $photo['alt'] ?: $member['tm_name'] ); ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
                <div class="team-card__body">
                    <h3><?php echo esc_html( $member['tm_name'] ); ?></h3>
                    <p class="team-card__role"><?php echo esc_html( $member['tm_role'] ); ?></p>
                    <?php if ( $member['tm_bio'] ) : ?><p><?php echo esc_html( $member['tm_bio'] ); ?></p><?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
