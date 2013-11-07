<?php

namespace modules\subscriptions\classes\models;

use core\classes\Model;
use core\classes\Encryption;

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
			return $subscription->getSellPrice();
		}

		return 0;
	}

	public function getCheckoutSubscription() {
		if (isset($this->objects['checkout_subscription'])) {
			return $this->objects['checkout_subscription']->getSellPrice();
		}

		$this->objects['checkout_subscription'] = $this->getModel('\modules\subscriptions\classes\models\CheckoutSubscription')->get(['subscription_id' => $this->id]);

		return $this->objects['checkout_subscription'];
	}

	public function getSubscription($customer_id) {
		$sql = "
			SELECT * FROM subscription
			WHERE subscription_id = (
				SELECT subscription_id FROM subscription
				WHERE
					customer_id=".$this->database->quote($customer_id)."
					AND subscription_expires > NOW()
				ORDER BY subscription_expires DESC
				LIMIT 1
			)
		";
		$record = $this->database->querySingle($sql);
		if ($record) {
			return $this->getModel(__CLASS__, $record);
		}
		else {
			return NULL;
		}
	}

	public function decodeReferenceNumber($reference) {
		return Encryption::defuscate($reference, $this->config->siteConfig()->secret);
	}

	public function getByReference($reference) {
		$subscription_id = $this->decodeReferenceNumber($reference);
		return $this->getModel(__CLASS__)->get(['id' => $subscription_id]);
	}

	public function getReferenceNumber() {
		return Encryption::obfuscate($this->id, $this->config->siteConfig()->secret);
	}
}
