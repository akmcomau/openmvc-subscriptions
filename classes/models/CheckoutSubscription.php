<?php

namespace modules\subscriptions\classes\models;

use core\classes\Model;

class CheckoutSubscription extends Model {

	protected $table       = 'checkout_subscription';
	protected $primary_key = 'checkout_subscription_id';
	protected $columns     = [
		'checkout_subscription_id' => [
			'data_type'      => 'int',
			'auto_increment' => TRUE,
			'null_allowed'   => FALSE,
		],
		'subscription_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'checkout_subscription_price' => [
			'data_type'      => 'numeric',
			'data_length'    => [6, 4],
			'null_allowed'   => FALSE,
		],
	];

	protected $indexes = [
		'subscription_id',
	];

	protected $foreign_keys = [
		'subscription_id' => ['subscription', 'subscription_id'],
	];
}
