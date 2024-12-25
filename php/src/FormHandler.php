<?php

class FormHandler
{
    private $data;
    private $api;
    public function __construct($data, $api)
    {
        $this->data = $data;
        $this->api = $api;
    }
    public function isFormDataValid()
    {
        return $this->isNameValid() && $this->isPhoneValid() && $this->isEmailValid() && $this->isPriceValid() && $this->isTimerValid();
    }

    private function isNameValid()
    {
        if (empty($this->data['price'])) return false;
        return (!is_numeric($this->data['name']) && is_string($this->data['name']));
    }

    private function isPhoneValid()
    {
        if (empty($this->data['phone'])) return false;
        $formattedPhone = preg_replace('/[^0-9]/', '', $this->data['phone']);
        return count($formattedPhone) == 11;
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
