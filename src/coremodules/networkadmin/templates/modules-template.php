<?php
/**
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
?>
<!-- MODULES -->
<div id="managemodules">
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<h2><?php _e( 'Modules', 'muneco' ) ?></h2>
		</div>
		<br class="clear">
	</div>

	<?php if ( count( $availableModules ) == 0 ): ?>
		<p>
			<?php _e( 'No Modules found', 'muneco' ) ?>.
		</p>
	<?php
	else:
		?>
		<table class="wp-list-table widefat fixed modules muneco-element" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" class="manage-column column-module" style="">
					<a href="#"><span><?php _e( 'Module', 'muneco' ) ?></span></a></th>
				<th scope="col" class="manage-column column-status" style="">
					<a href="#"><span><?php _e( 'Status', 'muneco' ) ?></span></a></th>
			</tr>
			</thead>

			<tfoot>
			<tr>
				<th scope="col" class="manage-column column-module" style="">
					<a href="#"><span><?php _e( 'Module', 'muneco' ) ?></span></a></th>
				<th scope="col" class="manage-column column-status" style="">
					<a href="#"><span><?php _e( 'Status', 'muneco' ) ?></span></a></th>
			</tr>
			</tfoot>

			<tbody id="the-list">
			<?php $counter = 0; ?>
			<?php
			foreach ( $availableModules as $modulename => $module ) :
				?>
				<tr class="<?php echo( $module['active'] ? "active" : "inactive" ); ?>">
					<td class="column-modulename modulename">
						<span><?php echo $module['Name']; ?></span>

						<p><?php echo $module['Description']; ?></p>
					</td>
					<td class="column-status status">
						<?php
						/**
						 * via POST -> not cacheable
						 */
						?>
						<?php if ( ! $module['active'] ) : ?>
							<form action="" method="POST"><input type="hidden" name="action" value="activate"><input
									type="hidden" name="module" value="<?php echo $modulename ?>"><input type="submit"
							                                                                             class="button button-large button-primary"
							                                                                             value="<?php _e( 'Activate', 'muneco' ); ?>">
							</form>
						<?php else: ?>
							<form action="" method="POST"><input type="hidden" name="action" value="deactivate"><input
									type="hidden" name="module" value="<?php echo $modulename ?>"><input type="submit"
							                                                                             class="button button-large"
							                                                                             value="<?php _e( 'Deactivate', 'muneco' ); ?>">
							</form>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php
	endif;
	?>
</div>
<!-- /MODULES -->