<?php
/**
 * Module: Overview
 * Part: Template
 *
 * @author     Patrick Puritscher
 * @license    GPL-2.0+
 * @link       -
 * @copyright  2014 Patrick Puritscher
 * @since      1.0
 */
?>
<div class="muneco-element grid">
	<?php
	foreach ( $allNodes as $nodeBlockID => $nodeBlock ) : /* Complete? */ {
		$nodeBlockComplete = true;
		if ( count( $nodeBlock ) < count( $enabledSites ) ) {
			$nodeBlockComplete = false;
		}
		?>
		<div class="col-4 box-container">
			<div class="connectionblock <?php echo( $nodeBlockComplete ? "complete" : "incomplete" ) ?>">
				<h3 class="hndle">
					<span><?php echo( $nodeBlockComplete ? __( "Complete", 'muneco' ) : __( "Incomplete", 'muneco' ) ) ?></span>
				</h3>

				<div class="inside">
					<ul id="connectionsblocklist-<?php echo $nodeBlockID; ?>">
						<?php foreach ( $enabledSites as $site ): ?>
							<?php if ( isset( $nodeBlock[ $site->blog_id ] ) && ! empty( $nodeBlock[ $site->blog_id ] ) ) : ?>
								<li>
									<b><?php echo $site->languagecode; ?>:</b>
									<?php //var_dump($nodeBlock[$site->blog_id]); ?>
									<span><?php echo $nodeBlock[ $site->blog_id ]->post_title; ?><?php echo " ($site->blog_id => " . $nodeBlock[ $site->blog_id ]->ID . ")"; ?></span>
								</li>
							<?php else: ?>
								<li class="missing">
									<span>Add - <?php echo $site->languagecode; ?></span>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	<?php } endforeach; ?>
</div>
