<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Config;
use Citfact\Form;
use Citfact\Form\Type\ParameterDictionary;

Loader::includeModule('citfact.form');

$app = Application::getInstance();
$params = new ParameterDictionary($arParams);
$result = new ParameterDictionary();

$builder = $params->get('BUILDER') ?: Config\Option::get('citfact.form', 'BUILDER');
$storage = $params->get('STORAGE') ?: Config\Option::get('citfact.form', 'STORAGE');
$validator = $params->get('VALIDATOR') ?: Config\Option::get('citfact.form', 'VALIDATOR');

$form = new Form\Form($params);
$form->register('builder', $builder);
$form->register('validator', $validator);
$form->register('storage', $storage);

$form->buildForm();
$form->handleRequest($app->getContext()->getRequest());
if ($form->isValid()) {
    $form->save();
}

$result->set('BUILDER', $form->getBuilder()->getBuilderData());
$result->set('SUCCESS', $form->isValid());
$result->set('ERRORS', $form->getErrors(false));
$result->set('REQUEST', $form->getRequestData());
$result->set('CSRF', $form->getCsrfToken());
$result->set('CAPTCHA', $form->getCaptchaToken());
$result->set('COMPONENT_ID', $form->getIdentifierToken());
$result->set('IS_POST', $form->getRequest()->isPost());
$result->set('IS_AJAX', (getenv('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest'));

if ($result->get('IS_AJAX') && $form->isSubmitted()) {
    $GLOBALS['APPLICATION']->restartBuffer();
    header('Content-Type: application/json');

    ob_start();
    $this->includeComponentTemplate();
    $bufferTemplate = ob_get_contents();
    ob_clean();

    $response = array(
        'success' => $result->get('SUCCESS'),
        'errors' => $result->get('ERRORS'),
        'captcha' => $result->get('CAPTCHA'),
        'html' => $bufferTemplate,
    );

    exit(json_encode($response));
}

$arResult = $result->toArray();
$this->includeComponentTemplate();