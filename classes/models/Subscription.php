<?php

namespace modules\subscriptions\classes\models;

use core\classes\Model;

class Subscription extends Model {

	protected $table       = 'subscription';
	protected $primary_key = 'subscription_id';
	protected $columns     = [
		'subscription_id' => [
			'data_type'      => 'int',
			'auto_increment' => TRUE,
			'null_allowed'   => FALSE,
		],
		'customer_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'subscription_type_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'subscription_created' => [
			'data_type'      => 'datetime',
			'null_allowed'   => FALSE,
		],
		'subscription_expires' => [
			'data_type'      => 'datetime',
			'null_allowed'   => FALSE,
		],
		'subscription_price' => [
			'data_type'      => 'numeric',
			'data_length'    => [6, 4],
			'null_allowed'   => FALSE,
		],
	];

	protected $indexes = [
		'customer_id',
		'subscription_type_id',
		'subscription_created',
		'subscription_expires',
	];

	protected $foreign_keys = [
		'customer_id'  => ['customer', 'customer_id'],
		'subscription_type_id'  => ['subscription_type', 'subscription_type_id'],
	];
}
