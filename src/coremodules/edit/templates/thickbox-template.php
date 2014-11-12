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
			       value="<?php _e( 'Update Connections', 'muneco' ); ?>"/>
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
				     class="connectionbox muneco-expand-container <?php echo( isset( $connections_junctions[ $site->blog_id ] ) ? "complete" : "incomplete" ) ?>">
					<h3 class="muneco-expand-toggle">
						<?php if ( isset( $connections_junctions[ $site->blog_id ] ) ) : ?>
							<?php echo $site->languagecode; ?>
							<span><?php echo $connections_junctions[ $site->blog_id ]->post_title; ?></span>
						<?php else: ?>
							<?php echo $site->languagecode; ?>
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
						<?php if ( false != $allPosts ) :
							foreach ( $allPosts[ $site->blog_id ] as $listelm ) : ?>
								<?php if (
									! isset( $listelm->connectedWith )
									|| ( isset( $listelm->connectedWith )
									     && ! array_key_exists( get_current_blog_id(), $listelm->connectedWith ) )
									|| ( isset( $listelm->connectedWith )
									     && array_key_exists( get_current_blog_id(), $listelm->connectedWith )
									     && $listelm->connectedWith[ get_current_blog_id() ] == get_the_ID() )
								): ?>
									<label class="post-connection">
										<input
											name="connect-<?php echo $site->blog_id; ?>"
											type="radio"
											data-lang="<?php echo $site->languagecode; ?>"
											data-siteid="<?php echo $site->blog_id; ?>"
											data-title="<?php echo $listelm->post_title; ?>"
											value="<?php echo $listelm->ID; ?>"
											<?php if ( isset( $connections_junctions[ $site->blog_id ] ) && $connections_junctions[ $site->blog_id ]->ID == $listelm->ID ): ?>
												checked="checked"
											<?php endif; ?>>
										<?php for ( $i = 0; $i < MuNeCo\count_post_anchestors( $site->blog_id, $listelm ); $i ++ ) : ?>
											&mdash;
										<?php endfor; ?>
										<?php echo $listelm->post_title; ?>
									</label><br>
								<?php endif; ?>
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
								<?php if ( ! isset( $connections_junctions[ $site->blog_id ] ) ): ?>
									checked="checked" <?php endif; ?>
								>
							<?php _e( 'No Connection', 'muneco' ); ?></label><br>
					</div>
				</div>

			<?php endforeach; ?>
		</div>
	</div>
</div>