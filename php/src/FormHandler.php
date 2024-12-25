<?php

use Monolog\Logger;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;

use AmoCRM\Models\ContactModel;
use League\OAuth2\Client\Token\AccessToken;

use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;

use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\CheckboxCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\CheckboxCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\CheckboxCustomFieldValueModel;

use AmoCRM\Models\LeadModel;
use AmoCRM\Collections\Leads\LeadsCollection;

class FormHandler
{
    private $data;
    private $api;
    public function __construct($data, AmoCRMApiClient $apiClient)
    {
        $this->data = $data;
        $this->api = $apiClient;
    }

    public function process(Logger $log)
    {
        if (!$this->isFormDataValid()) {
            http_response_code(400);
            die;
        }

        $token = new AccessToken([
            'access_token' => TOKEN,
            'expires' => 199999999999,
        ]);

        $this->api->setAccessToken($token)->setAccountBaseDomain(SUBDOMAIN);

        $log->info('Данные на входе', $this->data);

        $contact = new ContactModel();
        $contact->setName($this->data['name']);

        $contactCustomFields = $contact->getCustomFieldsValues();
        if (empty($contactCustomFields)) {
            $contactCustomFields = new CustomFieldsValuesCollection();
            $contact->setCustomFieldsValues($contactCustomFields);
        }

        //Получим значение поля по его коду
        $phoneField = $contactCustomFields->getBy('fieldCode', 'PHONE');

        //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
        if (empty($phoneField)) {
            $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
            $contactCustomFields->add($phoneField);
        }

        //Установим значение поля
        $phoneField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setEnum('WORK')
                        ->setValue($this->data['phone'])
                )
        );

        //Получим значение поля по его коду
        $emailField = $contactCustomFields->getBy('fieldCode', 'EMAIL');

        //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
        if (empty($emailField)) {
            $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
            $contactCustomFields->add($emailField);
        }

        //Установим значение поля
        $emailField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setEnum('WORK')
                        ->setValue($this->data['email'])
                )
        );

        $log->info('Тело контакта для создания:', $contact->toArray());
        sleep(1);
        try {
            $contactModel = $this->api->contacts()->addOne($contact);
        } catch (AmoCRMApiException $e) {
            $log->error($e);
            die;
        }

        $lead = new LeadModel();
        $lead->setName('Форма интеграции')
            ->setPrice($this->data['price'])
            ->setContacts(
                (new ContactsCollection())
                    ->add(
                        (new ContactModel())
                            ->setId($contactModel->getId())
                            ->setIsMain(true)
                    )
            );

        if (!empty($this->data['timer'])) {
            $leadCustomFields = $lead->getCustomFieldsValues();
            if (empty($leadCustomFields)) {
                $leadCustomFields = new CustomFieldsValuesCollection();
                $lead->setCustomFieldsValues($leadCustomFields);
            }

            //Получим значение поля по его коду
            $timerField = $leadCustomFields->getBy('fieldId', '951305');

            //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
            if (empty($timerField)) {
                $timerField = (new CheckboxCustomFieldValuesModel())->setFieldId('951305');
                $leadCustomFields->add($timerField);
            }

            //Установим значение поля
            $timerField->setValues(
                (new CheckboxCustomFieldValueCollection())
                    ->add(
                        (new CheckboxCustomFieldValueModel())
                            ->setValue($this->data['timer'])
                    )
            );
        }
        $leadsService = $this->api->leads();

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($lead);

        $log->info('Тело сделки для создания', $lead->toArray());

        sleep(1);
        try {
            $leadsCollection = $leadsService->add($leadsCollection);
        } catch (AmoCRMApiException $e) {
            $log->error($e);
            die;
        }
    }

    public function isFormDataValid()
    {
        return $this->isNameValid() && $this->isPhoneValid() && $this->isEmailValid() && $this->isPriceValid() && $this->isTimerValid();
    }

    private function isNameValid()
    {
        if (empty($this->data['name'])) return false;
        return (!is_numeric($this->data['name']) && is_string($this->data['name']) && preg_match('/^[\p{L}\s]+$/u', $this->data['name']));
    }

    private function isPhoneValid()
    {
        if (empty($this->data['phone'])) return false;
        $formattedPhone = preg_replace('/[^0-9]/', '', $this->data['phone']);
        return strlen($formattedPhone) == 11;
    }

    private function isPriceValid()
    {
        if (empty($this->data['price'])) return false;
        return is_numeric($this->data['price']);
    }

    private function isEmailValid()
    {
        if (empty($this->data['email'])) return false;
        return preg_match('/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u', $this->data['email']);
    }

    private function isTimerValid()
    {
        return empty($this->data['timer']) || (!empty($this->data['timer']) && is_numeric($this->data['timer']) && boolval($this->data['timer']));
    }
}
