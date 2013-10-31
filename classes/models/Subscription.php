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

	protected $relationships = [
		'customer' => [
			'where_fields'  => [
				'customer_first_name', 'customer_last_name',
				'customer_login', 'customer_email'
			],
			'join_clause'   => 'JOIN customer USING (customer_id)',
		],
	];

	public function getCustomer() {
		if (isset($this->objects['customer'])) {
			return $this->objects['customer'];
		}

		$this->objects['customer'] = $this->getModel('\core\classes\models\Customer')->get(['id' => $this->customer_id]);

		return $this->objects['customer'];
	}

	public function getType() {
		if (isset($this->objects['subscription_type'])) {
			return $this->objects['subscription_type'];
		}

		$this->objects['subscription_type'] = $this->getModel('\modules\subscriptions\classes\models\SubscriptionType')->get(['id' => $this->subscription_type_id]);

		return $this->objects['subscription_type'];
	}

	public function getPricePaid() {
		$subscription = $this->getCheckoutSubscription();
		if ($subscription) {
			return $subscription->price;
		}

		return 0;
	}

	public function getCheckoutSubscription() {
		if (isset($this->objects['checkout_subscription'])) {
			return $this->objects['checkout_subscription']->price;
		}

		$this->objects['checkout_subscription'] = $this->getModel('\modules\subscriptions\classes\models\CheckoutSubscription')->get(['subscription_id' => $this->id]);

		return $this->objects['checkout_subscription'];
	}
}
