<?php
/**
 * Module: Overview
 * Part: Template
 *
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
?>
<div class="MuNeCo-element col-4 box-container connectionlist-sidebar">
	<div class="connectionblock">
		<h3 class="hndle">
			<span>Not Connected</span>
		</h3>

		<div class="inside">
			<ul>
				<?php
				foreach ( $enabledSites as $site ) :
					?>
					<li>
						<b><?php echo $site->languagecode; ?></b>
						<ul>
							<?php
							foreach ( $junctions_notconnected[ $site->blog_id ] as $junction ) :
								?>
								<li>
									<span><?php echo $junction->post_title ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>