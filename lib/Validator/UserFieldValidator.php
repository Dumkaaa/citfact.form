<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\Form\Validator;

use Bitrix\Main\Request;
use Citfact\Form\FormValidatorInterface;

class UserFieldValidator implements FormValidatorInterface
{
    /**
     * @var array
     */
    protected $errorList = array();

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errorList;
    }

    /**
     * @inheritdoc
     */
    public function validate(Request $request, array $builderData)
    {
        $postRequest = $request->getPostList()->toArray();
        $highLoadCode = sprintf('HLBLOCK_%d', $builderData['DATA']['ID']);

        $this->getUserFieldManager()->editFormAddFields($highLoadCode, $postRequest);
        if (!$this->getUserFieldManager()->checkFields($highLoadCode, null, $postRequest)) {
            $exception = $GLOBALS['APPLICATION']->getException();
            $this->parseErrorList($exception);
        }
    }

    /**
     * @param $exception
     */
    protected function parseErrorList($exception)
    {
        foreach ($exception->messages as $error) {
            if (!array_key_exists($error['id'], $this->getErrors())) {
                $this->errorList[$error['id']] = $error['text'];
            }
        }
    }

    /**
     * @return \CUserTypeManager
     */
    protected function getUserFieldManager()
    {
        return new \CUserTypeManager();
    }
}