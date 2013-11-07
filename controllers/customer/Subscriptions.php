<?php

namespace modules\subscriptions\controllers\customer;

use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;

class Subscriptions extends Controller {

	protected $permissions = [
		'index' => ['customer'],
		'view' => ['customer'],
	];

	public function index() {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');

		$pagination = new Pagination($this->request, 'created', 'desc');


		// get all the subscription types
		$params = ['customer_id' => $this->authentication->getCustomerID()];
		$model  = new Model($this->config, $this->database);
		$subscription = $model->getModel('\modules\subscriptions\classes\models\Subscription');
		$subscriptions = $subscription->getMulti($params, $pagination->getOrdering(), $pagination->getLimitOffset());
		$pagination->setRecordCount($subscription->getCount($params));

		$message_js = NULL;
		switch($message) {
		}

		$data = [
			'subscriptions' => $subscriptions,
			'pagination' => $pagination,
			'message_js' => $message_js,
		];

		$template = $this->getTemplate('pages/customer/subscriptions.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

	public function view($reference) {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');

		$model = new Model($this->config, $this->database);
		$subscription = $model->getModel('\modules\subscriptions\classes\models\Subscription')->getByReference($reference);
		if (!$subscription || $subscription->customer_id != $this->authentication->getCustomerID()) {
			throw new RedirectException($this->getURL('administrator/Error', 'error_404'));
		}

		$data = [
			'subscription' => $subscription,
		];
		$template = $this->getTemplate('pages/customer/view.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

}