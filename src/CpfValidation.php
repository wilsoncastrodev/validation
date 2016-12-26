<?php
/**
 * Created by PhpStorm.
 * User: roberson.faria
 * Date: 26/12/16
 * Time: 10:57
 */

namespace RobersonFaria\Validation;


use Illuminate\Container\Container;
use Illuminate\Validation\Validator;

class CpfValidation extends Validator
{

    private $_custom_messages;

    public function __construct($translator, $data, $rules, $messages, $customAttributes)
    {
        parent::__construct($translator, $data, $rules, $messages, $customAttributes);

        $this->setMessage();
        $this->_set_custom_stuff();
    }

    public function app()
    {
        return Container::getInstance()->make('config');
    }

    public function setMessage()
    {
        $this->_custom_messages = ['cpf' => config('custom-validation.' . app()->getLocale() . ".cpf")];
    }

    protected function _set_custom_stuff()
    {
        $this->setCustomMessages($this->_custom_messages);
    }

    /**
     * função para validação do cpf retirada do http://www.geradorcpf.com/script-validar-cpf-php.htm
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function validateCpf($attribute, $value)
    {
        $cpf = $value;
        // Verifica se um número foi informado
        if (empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = preg_replace('/\D/', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        ) {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }
}