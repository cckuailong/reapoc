<?php get_header(); ?>

<div id="buycred-checkout-page">
	<div class="checkout-header">

		<?php buycred_checkout_title(); ?>

	</div>
	<div class="checkout-order">

		<form method="post" action="" id="buycred-checkout-form">

			<?php buycred_checkout_body(); ?>

		</form>

	</div>
	<div class="checkout-footer">

		<?php buycred_checkout_footer(); ?>

	</div>
</div>

<?php get_footer(); ?>