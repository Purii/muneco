<?php
/**
 * Template Thickbox
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
?>
<?php $ajax_nonce = wp_create_nonce( "muneco-ajax" ); ?>
<div id="tb-connectedpage" style="display: none;">
	<div id="tb-connectedpage-container">
		<p>
			<input type="button" class="button button-primary button-large updateConnections"
			       value="<?php _e( 'Accept connections', 'muneco' ); ?>"/>
			<input type="button" class="button cancelConnections"
			       value="<?php _e( 'Cancel' ); ?>"/>
		</p>

		<div id="connected-page-list-container" class="">
			<?php
			/* Not from Cache
			 * Except current Blog
			 */
			foreach ( $enabledSites as $site ) :
				?>
				<?php if ( $site->blog_id == get_current_blog_id() ) {
				continue;
			} ?>
				<div id="postslist-<?php echo $site->blog_id; ?>"
				     class="connectionbox muneco-expand-container <?php if ( isset( $connectedJunctions[ $site->blog_id ] ) ) {
					     echo "complete";
				     } else {
					     echo "incomplete";
				     } ?>">
					<h3 class="muneco-expand-toggle">
						<?php echo $site->languagecode; ?>
						<?php if ( isset( $connectedJunctions[ $site->blog_id ] ) ) : ?>
							<span><?php echo $connectedJunctions[ $site->blog_id ]->post_title; ?></span>
						<?php endif; ?>
					</h3>

					<div class="inside muneco-expand-inside">
						<div class="muneco-search">
							<input type="hidden" class="siteid" value="<?php echo $site->blog_id; ?>">
							<input type="hidden" class="ajax_nonce" value="<?php echo $ajax_nonce; ?>">
							<label>
								<span class="search-label"><?php _e( 'Search' ); ?></span>
								<input type="search" autocomplete="off"/>
								<span class="spinner"></span>
							</label>
						</div>
						<?php if ( !empty( $allPosts ) ) :
							foreach ( $allPosts[ $site->blog_id ] as $listelm ) : ?>
								<label class="post-connection">
									<input
										name="connect-<?php echo $site->blog_id; ?>"
										type="radio"
										data-lang="<?php echo $site->languagecode; ?>"
										data-siteid="<?php echo $site->blog_id; ?>"
										data-title="<?php echo $listelm->post_title; ?>"
										value="<?php echo $listelm->ID; ?>"
										<?php if ( isset( $connectedJunctions[ $site->blog_id ] ) && $connectedJunctions[ $site->blog_id ]->ID == $listelm->ID ): ?>
											checked="checked"
										<?php endif; ?>>
									<?php for ( $i = 0; $i < muneco\count_post_anchestors( $site->blog_id, $listelm ); $i ++ ) : ?>
										&mdash;
									<?php endfor; ?>
									<?php echo $listelm->post_title; ?>
								</label><br>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php /* No-Choice */ ?>
						<label class="post-connection">
							<input
								name="connect-<?php echo $site->blog_id; ?>"
								type="radio"
								data-lang="<?php echo $site->languagecode; ?>"
								data-siteid="<?php echo $site->blog_id; ?>"
								data-title="<?php _e( 'Not connected', 'muneco' ); ?>" value="0"
								<?php if ( ! isset( $connectedJunctions[ $site->blog_id ] ) ): ?>
									checked="checked" <?php endif; ?>
								>
							<?php _e( 'No Connection', 'muneco' ); ?></label><br>
					</div>
				</div>

			<?php endforeach; ?>
		</div>
	</div>
</div>