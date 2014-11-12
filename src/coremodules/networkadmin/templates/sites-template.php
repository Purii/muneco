<?php
/**
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
?>
<!-- SITES -->
<div id="managesites">
	<form method="post">
		<?php /* Call saveprocedure with this Name */ ?>
		<?php /* Is same as in MuNeConnector-Root */ ?>
		<input type="hidden" name="option_page" value="muneco_settingspage">
		<?php wp_nonce_field( 'muneco_settingspage', '_wpnonce' ) ?>

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<h2><?php _e( 'Manage Sites', 'muneco' ) ?>
				</h2>
			</div>
			<br class="clear">
		</div>

		<table class="wp-list-table widefat fixed sites muneco-element" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" class="manage-column column-path" style=""><a
						href="#"><span><?php echo( is_subdomain_install() ? __( 'Domain', 'muneco' ) : __( 'Path', 'muneco' ) ); ?></span></a>
				</th>
				<th scope="col" class="manage-column column-language" style=""><a
						href="#"><span><?php _e( 'Language', 'MuNeCo' ) ?></span></a></th>
				<th scope="col" class="manage-column column-status" style=""><a
						href="#"><span><?php _e( 'Status', 'MuNeCo' ) ?></span></a></th>
			</tr>
			</thead>

			<tfoot>
			<tr>
				<th scope="col" class="manage-column column-path" style=""><a
						href="#"><span><?php echo( is_subdomain_install() ? __( 'Domain', 'muneco' ) : __( 'Path', 'muneco' ) ); ?></span></a>
				</th>
				<th scope="col" class="manage-column column-language" style=""><a
						href="#"><span><?php _e( 'Language', 'MuNeCo' ) ?></span></a></th>
				<th scope="col" class="manage-column column-status" style=""><a
						href="#"><span><?php _e( 'Status', 'MuNeCo' ) ?></span></a></th>
			</tr>
			</tfoot>

			<tbody id="the-list">

			<?php $counter = 0; ?>
			<?php $blogIDs = array(); ?>
			<?php foreach ( $allSites as $site ) : ?>
				<?php /** Merge with WP-Settings **/
				$availabiliyClass = '';
				/** WP-Settings **/
				if ( 0 == $site['public'] ) {
					$availabiliyClass = 'warning ';
				}
				/** MuNeCo-Settings */
				if ( "1" == $site['munecostatus'] ) {
					$availabiliyClass .= 'active';
				} else {
					$availabiliyClass .= 'inactive';
				}
				?>
				<tr class="<?php echo $availabiliyClass; ?>">
					<?php array_push( $blogIDs, $site['blog_id'] ); ?>
					<td class="column-blogname blogname">
				<span>
					<?php echo( $isSubdomaininstall ? $site['domain'] : $site['path'] ); ?>
				</span>
					</td>
					<td class="column-language language">
						<input type="text" name="muneco_languagecode_<?php echo $site['blog_id'] ?>"
						       value="<?php echo $site['languagecode']; ?>" placeholder="en">
					</td>
					<td class="column-status status">
						<label><input type="checkbox" value="1"
						              name="muneco_status_<?php echo $site['blog_id'] ?>" <?php checked( 1, $site['munecostatus'], true ) ?>><?php _e( 'Enable Connections', 'muneco' ) ?>
						</label>
						<?php if ( 0 == $site['public'] ) {
							?>
							<p class="hint"><?php _e( 'Not marked as public', 'muneco' ) ?></p>
						<?php
						}
						?>
					</td>
				</tr>

			<?php endforeach; ?>
			</tbody>
		</table>
		<?php /* Send all sites */ ?>
		<input type="hidden" name="siteIDs" value='<?php echo base64_encode( serialize( $blogIDs ) ); ?>'>
		<?php submit_button(); ?>
	</form>
</div>
<!-- /SITES -->