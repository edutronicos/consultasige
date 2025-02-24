<?php

use Adianti\Database\TRecord;

class Pessoal extends TRecord
{
    const TABLENAME = 'Pessoal';
    const PRIMARYKEY = 'Pessoal_Codigo';
    const IDPOLICY = 'serial';

    private $banco_codigo;
    private $agencia_conta;

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

    function get_banco()
    {
        if (empty($this->banco_codigo)) {
            $this->banco_codigo = new Banco($this->Banco_Codigo);
        }
        return $this->banco_codigo;
    }

    function get_banco_descricao()
    {
        if (empty($this->banco_codigo)) {
            $this->banco_codigo = new Banco($this->Banco_Codigo);
        }
        return $this->banco_codigo->Banco_Descricao;
    }

    public function get_dados_banco()
    {
        $banco = $this->get_banco_descricao();
        $agencia = $this->Pessoal_Agencia;
        $digito_agencia = $this->Pessoal_DigitoAgencia;
        $conta = $this->Pessoal_Conta;
        $digito_conta = $this->Pessoal_DigitoConta;

        return $banco . ' - Agência: ' . $agencia . '-' . $digito_agencia . ' - Conta: ' . $conta . '-' . $digito_conta;
    }
}