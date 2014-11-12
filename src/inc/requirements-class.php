<?php

/**
 * Fired every time the Plugin starts
 * Performance?
 *
 * @package   MuNeCo
 * @author    Patrick Puritscher
 * @license   GPL-2.0+
 * @link      -
 * @copyright 2014 Patrick Puritscher
 */
class MuNeCo_requirements {

	/**
	 *
	 * @var string
	 */
	private $req_phpv = '5.3';

	/**
	 * @var array
	 */
	private $errormessages = array();

	/**
	 * @return bool
	 */
	private function check_version() {
		return version_compare( PHP_VERSION, $this->req_phpv ) >= 0;
	}

	/**
	 * @return bool
	 */
	public function check() {
		if ( $this->check_version() ) {
			return true;
		}

		$this->errormessages[] = array(
			'single'   => sprintf( __( 'MuNeCo requires at least %s', 'MuNeCo' ), '<strong>PHP ' . $this->req_phpv . '</strong>' ),
			'multiple' => sprintf( __( 'and at least %s', 'muneco' ), '<strong>PHP ' . $this->req_phpv . '</strong>' )
		);

		return false;
	}

	/**
	 * Displays admin notice
	 */
	public function admin_notice() {
		?>
		<div class="error">
			<p>
				<?php
				end( $this->errormessages );
				$lastmessage = current( $this->errormessages );
				reset( $this->errormessages );
				$firstmessage = current( $this->errormessages );

				foreach ( $this->errormessages as $errormessage ) {

					if ( $firstmessage === $errormessage ) {
						if ( isset( $errormessage["single"] ) ) {
							echo $errormessage["single"];
						} else {
							echo $errormessage;
						}
						continue;
					}
					if ( current( $this->errormessages ) !== next( $this->errormessages ) ) {
						echo ' <i>' . __( 'AND', 'MuNeCo' ) . '</i> ';
						if ( isset( $errormessage["multiple"] ) ) {
							echo $errormessage["multiple"];
						} else if ( isset( $errormessage["single"] ) ) {
							echo $errormessage["single"];
						} else {
							echo $errormessage;
						}
					}
				}
				?>
			</p>

			<p><?php _e( 'Please uninstall MuNeCo properly using the Plugin-Manager', 'muneco' ); ?></p>
		</div>
	<?php
	}

	/**
	 * Trigger notice display
	 */
	public function display_admin_notice() {
		add_action( 'network_admin_notices', array( $this, 'admin_notice' ) );
	}
} 