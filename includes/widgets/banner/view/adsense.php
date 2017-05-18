<?php
/**
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Tyche_Companion
 * @subpackage Tyche_Companion/includes
 */

// If the image is not set, terminate here
if ( empty( $params['image'] ) ) {
	return false;
}
?>

<div class="row">
	<div class="col-xs-12 tyche-adsense-banner">
		<?php echo $params['adsense_code'] ?>
	</div>
</div>
