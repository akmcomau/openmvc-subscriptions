<?php

namespace modules\subscriptions\classes\models;

use core\classes\Model;
use modules\checkout\classes\models\ItemInterface;
use modules\checkout\classes\models\Checkout;
use modules\checkout\classes\models\CheckoutItem;

class SubscriptionType extends Model implements ItemInterface {

	protected $quantity = 0;
	protected $total = 0;

	protected $table       = 'subscription_type';
	protected $primary_key = 'subscription_type_id';
	protected $columns     = [
		'subscription_type_id' => [
			'data_type'      => 'int',
			'auto_increment' => TRUE,
			'null_allowed'   => FALSE,
		],
		'site_id' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'subscription_type_active' => [
			'data_type'      => 'bool',
			'null_allowed'   => FALSE,
			'default_value'  => 'TRUE',
		],
		'subscription_type_name' => [
			'data_type'      => 'text',
			'data_length'    => 128,
			'null_allowed'   => FALSE,
		],
		'subscription_type_description' => [
			'data_type'      => 'text',
			'data_length'    => 256,
			'null_allowed'   => FALSE,
		],
		'subscription_type_price' => [
			'data_type'      => 'numeric',
			'data_length'    => [6, 4],
			'null_allowed'   => FALSE,
		],
		'subscription_type_period' => [
			'data_type'      => 'text',
			'data_length'    => 10,
			'null_allowed'   => FALSE,
		],
		'subscription_type_period_length' => [
			'data_type'      => 'int',
			'null_allowed'   => FALSE,
		],
		'subscription_type_data' => [
			'data_type'      => 'text',
			'null_allowed'   => TRUE,
		],
	];

	protected $indexes = [
		'site_id',
		'subscription_type_active',
	];

	public function getPeriodString($language) {
		$string = $this->period_length.' ';
		if ($this->period == 'months') {
			$string .= ($this->period_length > 1) ? $language->get('months') : $language->get('month');
		}
		else {
			$string .= ($this->period_length > 1) ? $language->get('days') : $language->get('day');
		}
		return $string;
	}

	public function purchase(Checkout $checkout, CheckoutItem $checkout_item, ItemInterface $item) {
		$subscription = $this->getModel('\modules\subscriptions\classes\models\Subscription')->getSubscription($checkout->customer_id);
		if ($subscription) {
			$expiry = $subscription->expires;
		}
		else {
			$expiry = 'now';
		}

		$subscription = $this->getModel('\modules\subscriptions\classes\models\Subscription');
		$subscription->customer_id = $checkout->customer_id;
		$subscription->type_id     = $checkout_item->type_id;
		$subscription->expires     = date('c', strtotime($expiry.' + '.$item->period_length.' '.$item->period));
		$subscription->insert();

		$checkout_sub = $this->getModel('\modules\subscriptions\classes\models\CheckoutSubscription');
		$checkout_sub->checkout_item_id = $checkout_item->id;
		$checkout_sub->subscription_id = $subscription->id;
		$checkout_sub->insert();
	}

	public function allowMultiple() {
		return FALSE;
	}


	public function getMaxQuantity() {
		return 1;
	}

	public function getName() {
		return $this->name;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getSKU() {
		return 'SUB-'.str_pad($this->id, 4, '0', STR_PAD_LEFT);
	}

	public function setQuantity($quantity) {
		$this->quantity = (int)$quantity;
	}

	public function getQuantity() {
		return $this->quantity;
	}

	public function setTotal($total) {
		$this->total = (int)$total;
	}

	public function getTotal() {
		return $this->total;
	}

	public function getType() {
		return 'subscription';
	}

	public function getCostPrice() {
		return 0;
	}

	public function isShippable() {
		return FALSE;
	}
}
