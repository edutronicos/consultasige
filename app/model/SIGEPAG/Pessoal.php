<?php

use Adianti\Database\TRecord;

class Pessoal extends TRecord
{
    const TABLENAME = 'Pessoal';
    const PRIMARYKEY = 'Pessoal_Codigo';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('Pessoal_Codigo');
        parent::addAttribute('Pessoal_Nome');
        parent::addAttribute('Pessoal_CTPS');
        parent::addAttribute('Pessoal_Serie');
        parent::addAttribute('Pessoal_CTPSUF');
        parent::addAttribute('Pessoal_CARTAO');
        parent::addAttribute('Pessoal_Usuario');
        parent::addAttribute('Pessoal_Incluido');
        parent::addAttribute('FormaPgto_Codigo');
        parent::addAttribute('Pessoal_Agencia');
        parent::addAttribute('Pessoal_DigitoAgencia');
        parent::addAttribute('Pessoal_Conta');
        parent::addAttribute('Pessoal_DigitoConta');
        parent::addAttribute('Pessoal_Favorecido');
        parent::addAttribute('Banco_Codigo');
        parent::addAttribute('Pessoal_TipoConta');
        parent::addAttribute('Pessoal_CPF');
    }
}