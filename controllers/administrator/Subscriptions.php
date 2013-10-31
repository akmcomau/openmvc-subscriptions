<?php

namespace modules\subscriptions\controllers\administrator;

use core\classes\exceptions\RedirectException;
use core\classes\exceptions\SoftRedirectException;
use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;
use modules\subscriptions\classes\models\SubscriptionType;
use modules\subscriptions\classes\models\Subscription;

class Subscriptions extends Controller {

	protected $show_admin_layout = TRUE;

	protected $permissions = [
		'config' => ['administrator'],
		'index' => ['administrator'],
		'addSubscription' => ['administrator'],
		'editSubscription' => ['administrator'],
		'deleteSubscription' => ['administrator'],
		'types' => ['administrator'],
		'editType' => ['administrator'],
		'addType' => ['administrator'],
		'deleteType' => ['administrator'],
		'report' => ['administrator'],
	];

	public function config() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
		$this->response->setContent($template->render());
	}

	public function index($message = NULL) {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');
		$form_search = $this->getSubscriptionSearchForm();

		$pagination = new Pagination($this->request, 'name');

		$params = ['site_id' => ['type'=>'in', 'value'=>$this->allowedSiteIDs()]];
		if ($form_search->validate()) {
			$values = $form_search->getSubmittedValues();
			foreach ($values as $name => $value) {
				if (preg_match('/^search_(first_name|last_name|email|login)$/', $name, $matches) && $value != '') {
					$value = strtolower($value);
					$params['customer_'.$matches[1]] = ['type'=>'like', 'value'=>'%'.$value.'%'];
				}
			}
		}

		// get all the subscription types
		$model  = new Model($this->config, $this->database);
		$subscription = $model->getModel('\modules\subscriptions\classes\models\Subscription');
		$subscriptions = $subscription->getMulti($params, $pagination->getOrdering(), $pagination->getLimitOffset());
		$pagination->setRecordCount($subscription->getCount($params));

		$message_js = NULL;
		switch($message) {
			case 'delete-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_subscription_delete_success')).'");';
				break;

			case 'add-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_subscription_add_success')).'");';
				break;

			case 'update-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_subscription_update_success')).'");';
				break;
		}

		$data = [
			'form' => $form_search,
			'subscriptions' => $subscriptions,
			'pagination' => $pagination,
			'message_js' => $message_js,
		];

		$template = $this->getTemplate('pages/administrator/list_subscriptions.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

	public function addSubscription() {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');
		$form = $this->getSubscriptionForm(TRUE);

		$model = new Model($this->config, $this->database);
		$subscription = $model->getModel('\modules\subscriptions\classes\models\Subscription');

		if ($form->validate()) {
			$this->updateSubscriptionFromRequest($form, $subscription, TRUE);
			$subscription->insert();
			throw new RedirectException($this->url->getURL('administrator/Subscriptions', 'index', ['add-success']));
		}
		elseif ($form->isSubmitted()) {
			$this->updateSubscriptionFromRequest($form, $subscription, TRUE);
			$form->setNotification('error', $this->language->get('notification_subscription_add_error'));
		}

		$type = $model->getModel('\modules\subscriptions\classes\models\SubscriptionType');
		$types = $type->getMulti([], ['name' => 'asc']);

		$data['is_add_page'] = TRUE;
		$data['form'] = $form;
		$data['types'] = $types;
		$data['subscription'] = $subscription;
		$template = $this->getTemplate('pages/administrator/add_edit_subscription.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

	public function editSubscription($subscription_id) {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');
		$form = $this->getSubscriptionForm(FALSE);

		$model = new Model($this->config, $this->database);
		$subscription = $model->getModel('\modules\subscriptions\classes\models\Subscription')->get(['id' => $subscription_id]);
		$this->siteProtection($subscription, 'getCustomer');

		if ($form->validate()) {
			$this->updateSubscriptionFromRequest($form, $subscription, FALSE);
			$subscription->update();
			throw new RedirectException($this->url->getURL('administrator/Subscriptions', 'index', ['add-success']));
		}
		elseif ($form->isSubmitted()) {
			$this->updateSubscriptionFromRequest($form, $subscription, FALSE);
			$form->setNotification('error', $this->language->get('notification_subscription_add_error'));
		}

		$type = $model->getModel('\modules\subscriptions\classes\models\SubscriptionType');
		$types = $type->getMulti([], ['name' => 'asc']);

		$data['is_add_page'] = FALSE;
		$data['form'] = $form;
		$data['types'] = $types;
		$data['subscription'] = $subscription;
		$template = $this->getTemplate('pages/administrator/add_edit_subscription.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

	public function deleteSubscription() {
		if ($this->request->requestParam('selected')) {
			$model = new Model($this->config, $this->database);
			$type = $model->getModel('\modules\subscriptions\classes\models\Subscription');
			foreach ($this->request->requestParam('selected') as $id) {
				$sub_type = $type->get(['id' => $id]);
				$this->siteProtection($sub_type, 'getCustomer');
				$sub_type->delete();
			}

			throw new RedirectException($this->url->getURL('administrator/Subscriptions', 'index', ['delete-success']));
		}
	}

	protected function updateSubscriptionFromRequest(FormValidator $form, Subscription $subscription, $is_add_page) {
		if ($is_add_page) {
			$customer = $subscription->getModel('\core\classes\models\Customer')->get(['email' => $form->getValue('customer_email')]);
			if ($customer) {
				$subscription->customer_id = $customer->id;
			}

			$subscription->subscription_type_id = $form->getValue('subscription_type');
			$type = $subscription->getModel('\modules\subscriptions\classes\models\SubscriptionType')->get(['id' => (int)$form->getValue('subscription_type')]);

			$subscription->expires = date('Y-m-d H:i:s', strtotime('now + '.$type->period_length.' '.$type->period));
		}
		else {
			$subscription->expires = $form->getValue('expiry').' 23:59:59';
		}
	}

	public function types($message = NULL) {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');
		$form_search = $this->getSubscriptionTypeSearchForm();

		$pagination = new Pagination($this->request, 'name');

		$params = ['site_id' => ['type'=>'in', 'value'=>$this->allowedSiteIDs()]];
		if ($form_search->validate()) {
			$values = $form_search->getSubmittedValues();
			foreach ($values as $name => $value) {
				if ($name == 'search_name' && strlen($value) > 0) {
					$params['name'] = ['type'=>'like', 'value'=>'%'.$value.'%'];
				}
			}
		}

		// get all the subscription types
		$model  = new Model($this->config, $this->database);
		$type = $model->getModel('\modules\subscriptions\classes\models\SubscriptionType');
		$types = $type->getMulti($params, $pagination->getOrdering(), $pagination->getLimitOffset());
		$pagination->setRecordCount($type->getCount($params));

		$message_js = NULL;
		switch($message) {
			case 'delete-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_type_delete_success')).'");';
				break;

			case 'add-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_type_add_success')).'");';
				break;

			case 'update-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_type_update_success')).'");';
				break;
		}

		$data = [
			'form' => $form_search,
			'types' => $types,
			'pagination' => $pagination,
			'message_js' => $message_js,
		];

		$template = $this->getTemplate('pages/administrator/list_types.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

	public function addType() {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');
		$form = $this->getSubscriptionTypeForm();

		$model = new Model($this->config, $this->database);
		$type = $model->getModel('\modules\subscriptions\classes\models\SubscriptionType');
		$type->site_id = $this->config->siteConfig()->site_id;

		if ($form->validate()) {
			$this->updateTypeFromRequest($form, $type);
			$type->insert();
			throw new RedirectException($this->url->getURL('administrator/Subscriptions', 'types', ['add-success']));
		}
		elseif ($form->isSubmitted()) {
			$this->updateTypeFromRequest($form, $type);
			$form->setNotification('error', $this->language->get('notification_add_error'));
		}

		$data['is_add_page'] = TRUE;
		$data['form'] = $form;
		$data['type'] = $type;
		$template = $this->getTemplate('pages/administrator/add_edit_type.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

	public function editType($type_id) {
		$this->language->loadLanguageFile('administrator/subscriptions.php', 'modules'.DS.'subscriptions');
		$form = $this->getSubscriptionTypeForm();

		$model = new Model($this->config, $this->database);
		$type = $model->getModel('\modules\subscriptions\classes\models\SubscriptionType')->get([
			'id' => (int)$type_id,
		]);
		$this->siteProtection($type);

		if ($form->validate()) {
			$this->updateTypeFromRequest($form, $type);
			$type->update();
			throw new RedirectException($this->url->getURL('administrator/Subscriptions', 'types', ['update-success']));
		}
		elseif ($form->isSubmitted()) {
			$this->updateTypeFromRequest($form, $type);
			$form->setNotification('error', $this->language->get('notification_update_error'));
		}

		$data['is_add_page'] = FALSE;
		$data['form'] = $form;
		$data['type'] = $type;
		$template = $this->getTemplate('pages/administrator/add_edit_type.php', $data, 'modules'.DS.'subscriptions');
		$this->response->setContent($template->render());
	}

	public function deleteType() {
		if ($this->request->requestParam('selected')) {
			$model = new Model($this->config, $this->database);
			$type = $model->getModel('\modules\subscriptions\classes\models\SubscriptionType');
			foreach ($this->request->requestParam('selected') as $id) {
				$sub_type = $type->get(['id' => $id]);
				$this->siteProtection($sub_type);
				$sub_type->delete();
			}

			throw new RedirectException($this->url->getURL('administrator/Subscriptions', 'types', ['delete-success']));
		}
	}

	public function report() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
		$this->response->setContent($template->render());
	}

	protected function updateTypeFromRequest(FormValidator $form, SubscriptionType $type) {
		$type->name = $form->getValue('name');
		$type->description = $form->getValue('description');
		$type->period = $form->getValue('period');
		$type->period_length = (int)$form->getValue('period_length');
		$type->price = $form->getValue('price');
		$type->active = (int)$form->getValue('active') ? TRUE : FALSE;
	}

	protected function getSubscriptionSearchForm() {
		$inputs = [
			'search_first_name' => [
				'type' => 'string',
				'required' => FALSE,
				'max_length' => 256,
				'message' => $this->language->get('error_search_first_name'),
			],
			'search_last_name' => [
				'type' => 'string',
				'required' => FALSE,
				'max_length' => 256,
				'message' => $this->language->get('error_search_last_name'),
			],
			'search_login' => [
				'type' => 'string',
				'required' => FALSE,
				'max_length' => 32,
				'message' => $this->language->get('error_search_login'),
			],
			'search_email' => [
				'type' => 'string',
				'required' => FALSE,
				'max_length' => 256,
				'message' => $this->language->get('error_search_email'),
			],
		];

		return new FormValidator($this->request, 'form-subscription-search', $inputs);
	}

	protected function getSubscriptionForm($is_add_page) {
		$model  = new Model($this->config, $this->database);
		if ($is_add_page) {
			$inputs = [
				'customer_email' => [
					'type' => 'email',
					'required' => TRUE,
					'message' => $this->language->get('error_customer_email'),
				],
				'subscription_type' => [
					'type' => 'integer',
					'required' => TRUE,
					'message' => $this->language->get('error_subscription_type'),
				],
			];

			$validators = [
				'customer_email' => [
					[
						'type'     => 'function',
						'message'  => $this->language->get('error_email_not_found'),
						'function' => function($value) use ($model) {
							$customer = $model->getModel('core\classes\models\Customer');
							$customer = $customer->get(['email' => $value]);
							return $customer ? TRUE : FALSE;
						}
					],
				],
			];
		}
		else {
			$inputs = [
				'expiry' => [
					'type' => 'date',
					'required' => TRUE,
					'message' => $this->language->get('error_expiry'),
				],
			];

			$validators = [

			];
		}

		return new FormValidator($this->request, 'form-subscription', $inputs, $validators);
	}

	protected function getSubscriptionTypeSearchForm() {
		$inputs = [
			'search_name' => [
				'type' => 'string',
				'required' => FALSE,
				'max_length' => 256,
				'message' => $this->language->get('error_search_name'),
			],
		];

		return new FormValidator($this->request, 'form-subscription-type-search', $inputs);
	}

	protected function getSubscriptionTypeForm() {
		$inputs = [
			'name' => [
				'type' => 'string',
				'required' => TRUE,
				'max_length' => 128,
				'message' => $this->language->get('error_name'),
			],
			'description' => [
				'type' => 'string',
				'required' => TRUE,
				'max_length' => 256,
				'message' => $this->language->get('error_description'),
			],
			'period' => [
				'type' => 'string',
				'required' => TRUE,
				'message' => $this->language->get('error_period'),
			],
			'period_length' => [
				'type' => 'integer',
				'required' => TRUE,
				'message' => $this->language->get('error_period_length'),
			],
			'price' => [
				'type' => 'money',
				'required' => TRUE,
				'zero_allowed' => TRUE,
				'message' => $this->language->get('error_price'),
			],
			'active' => [
				'type' => 'integer',
				'required' => TRUE,
				'message' => $this->language->get('error_active'),
			],
		];

		return new FormValidator($this->request, 'form-subscription-type', $inputs);
	}

}