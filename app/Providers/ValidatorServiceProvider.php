<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Validação de CPF
        Validator::extend('cpf', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace('/[^0-9]/', '', $value);
            
            // Verifica se o CPF tem 11 dígitos
            if (strlen($value) != 11) {
                return false;
            }
            
            // Verifica se todos os dígitos são iguais
            if (preg_match('/(\d)\1{10}/', $value)) {
                return false;
            }
            
            // Validação do primeiro dígito verificador
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                $sum += (int)$value[$i] * (10 - $i);
            }
            $remainder = $sum % 11;
            $digit1 = $remainder < 2 ? 0 : 11 - $remainder;
            
            if ($value[9] != $digit1) {
                return false;
            }
            
            // Validação do segundo dígito verificador
            $sum = 0;
            for ($i = 0; $i < 10; $i++) {
                $sum += (int)$value[$i] * (11 - $i);
            }
            $remainder = $sum % 11;
            $digit2 = $remainder < 2 ? 0 : 11 - $remainder;
            
            return $value[10] == $digit2;
        });

        // Mensagem de erro para CPF
        Validator::replacer('cpf', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'O :attribute informado não é válido.');
        });

        // Validação de celular com DDD
        Validator::extend('celular_com_ddd', function ($attribute, $value, $parameters, $validator) {
            $value = preg_replace('/[^0-9]/', '', $value);
            
            // Verificar se tem entre 10 e 11 dígitos (com ou sem 9 na frente)
            return strlen($value) >= 10 && strlen($value) <= 11;
        });

        // Mensagem de erro para celular
        Validator::replacer('celular_com_ddd', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'O :attribute deve ser um número de celular válido com DDD.');
        });

        // Validação de formato de CEP
        Validator::extend('formato_cep', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]{5}-?[0-9]{3}$/', $value);
        });

        // Mensagem de erro para CEP
        Validator::replacer('formato_cep', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'O :attribute deve estar no formato 00000-000.');
        });
    }
}
