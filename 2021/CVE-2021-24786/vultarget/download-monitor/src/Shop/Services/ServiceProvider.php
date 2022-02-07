<?php

namespace Never5\DownloadMonitor\Shop\Services;

use Never5\DownloadMonitor\Dependencies\Pimple;
use Never5\DownloadMonitor\Dependencies\Pimple\Container;
use Never5\DownloadMonitor\Shop;

class ServiceProvider implements Pimple\ServiceProviderInterface {

	/**
	 * Register our DLM E-Commerce services
	 *
	 * @param Container $container
	 */
	public function register( Container $container ) {

		$container['currency'] = function ( $c ) {
			return new Shop\Helper\Currency();
		};

		$container['country'] = function ( $c ) {
			return new Shop\Helper\Country();
		};

		$container['format'] = function ( $c ) {
			return new Shop\Helper\Format();
		};

		$container['product_factory'] = function ( $c ) {
			return new Shop\Product\Factory();
		};

		$container['product_repository'] = function ( $c ) {
			return new Shop\Product\WordPressRepository();
		};

		$container['session_cookie'] = function ( $c ) {
			return new Shop\Session\Cookie();
		};

		$container['session_repository'] = function ( $c ) {
			return new Shop\Session\WordPressRepository();
		};

		$container['session_factory'] = function ( $c ) {
			return new Shop\Session\Factory();
		};

		$container['session_item_factory'] = function ( $c ) {
			return new Shop\Session\Item\Factory();
		};

		$container['session'] = function ( $c ) {
			return new Shop\Session\Manager();
		};

		$container['tax_class_manager'] = function ( $c ) {
			return new Shop\Tax\TaxClassManager();
		};

		$container['cart'] = function ( $c ) {
			return new Shop\Cart\Manager();
		};

		$container['cart_item_factory'] = function ( $c ) {
			return new Shop\Cart\Item\Factory();
		};

		$container['page'] = function ( $c ) {
			return new Shop\Util\Page();
		};

		$container['redirect'] = function ( $c ) {
			return new Shop\Util\Redirect();
		};

		$container['checkout_field'] = function ( $c ) {
			return new Shop\Checkout\Field();
		};

		$container['payment_gateway'] = function ( $c ) {
			return new Shop\Checkout\PaymentGateway\Manager();
		};

		$container['order'] = function ( $c ) {
			return new Shop\Order\Manager();
		};

		$container['order_factory'] = function ( $c ) {
			return new Shop\Order\Factory();
		};

		$container['order_repository'] = function ( $c ) {
			return new Shop\Order\WordPressRepository();
		};

		$container['order_status'] = function ( $c ) {
			return new Shop\Order\Status\Manager();
		};

		$container['order_status_factory'] = function ( $c ) {
			return new Shop\Order\Status\Factory();
		};

		$container['order_transaction_factory'] = function ( $c ) {
			return new Shop\Order\Transaction\Factory();
		};

		$container['email'] = function ( $c ) {
			return new Shop\Email\Handler();
		};
	}


}