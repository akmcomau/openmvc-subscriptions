<?php

namespace modules\subscriptions\controllers\administrator;

use core\classes\exceptions\RedirectException;
use core\classes\exceptions\SoftRedirectException;
use core\classes\renderable\Controller;
use core\classes\Model;
use core\classes\Pagination;
use core\classes\FormValidator;
use modules\subscriptions\classes\models\SubscriptionType;

class Subscriptions extends Controller {

	protected $show_admin_layout = TRUE;

	protected $permissions = [
		'config' => ['administrator'],
	];

	public function index() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
		$this->response->setContent($template->render());
	}

	public function config() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
		$this->response->setContent($template->render());
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
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_delete_success')).'");';
				break;

			case 'add-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_add_success')).'");';
				break;

			case 'update-success':
				$message_js = 'FormValidator.displayPageNotification("success", "'.htmlspecialchars($this->language->get('notification_update_success')).'");';
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

	public function add() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
		$this->response->setContent($template->render());
	}

	public function edit() {
		$this->language->loadLanguageFile('administrator/skeleton.php', 'core'.DS.'modules'.DS.'skeleton');
		$template = $this->getTemplate('pages/administrator/skeleton.php', [], 'core'.DS.'modules'.DS.'skeleton');
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
			$form->setNotification('error', $this->language->get('notification_add_error'));
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