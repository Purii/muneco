<?php
/**
 * Template Publishbox
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
?>
<div class="misc-pub-section misc-pub-post-connections misc-pub-section-last" style="border-top: 1px solid #eee;">

	<?php wp_nonce_field( MUNECO_BASENAME, 'post_connections_nonce' ); ?>
	<span id="display-post-connections"><?php _e( 'Connections', 'muneco' ); ?>:</span>
	<span id="post-connections-display"> <b id="post-connections-counter">0</b>
	</span>
	<a href="#post_connections" class="edit-post-connections hide-if-no-js"><?php _e( 'Connect', 'muneco' ); ?> </a>

	<div id="post-status-connections" class="hide-if-js">
		<ul>
			<?php foreach ( $enabledSites as $site ) : ?>
				<?php /* Prevent Displaying current Blog */ ?>
				<?php if ( $site->blog_id == get_current_blog_id() ) {
					continue;
				} ?>
				<li data-lang="<?php echo $site->languagecode; ?>" data-siteid="<?php echo $site->blog_id; ?>">
					<label
						for="post-connection-<?php echo $site->blog_id; ?>">
						<b><?php echo $site->languagecode; ?>:</b>
					</label> <input class="post-connection-hidden" hidden="hidden"
					                id="post-connection-<?php echo $site->blog_id; ?>"
					                name="post-connection-<?php echo $site->blog_id; ?>"
					                value="
				<?php echo( isset( $connections_junctions[ $site->blog_id ] ) ? $connections_junctions[ $site->blog_id ]->ID : '0' ); ?>
						"> <a
						href="#TB_inline?width=500&height=300&inlineId=tb-connectedpage"
						class="thickbox post-connection-previewtext"
						title="<?php _e( 'Please choose the elements you want to connect', 'muneco' ); ?>"> <?php echo( isset( $connections_junctions[ $site->blog_id ] ) ? $connections_junctions[ $site->blog_id ]->post_title : __( 'Not connected', 'muneco' ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<a href="#post_connections"
			   class="save-post-connections hide-if-no-js button"><?php _e( 'OK ' ); ?>
			</a> <a href="#post_connections"
			        class="cancel-post-connections hide-if-no-js button-cancel"><?php _e( 'Cancel' ); ?>
			</a>
		</p>
	</div>
</div>