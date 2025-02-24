<?php

use Adianti\Database\TRecord;

class CCorrente extends TRecord
{
    const TABLENAME = 'CCorrente';
    const PRIMARYKEY = 'CCorrente_Codigo';
    const IDPOLICY = 'serial';

    private $pessoa_banco;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('CCorrente_Codigo');
        parent::addAttribute('CCorrente_CodFolha');
        parent::addAttribute('CCorrente_LocalCLi');
        parent::addAttribute('CCOrrente_CCustoCli');
        parent::addAttribute('CCorrente_Inicio');
        parent::addAttribute('CCorrente_Fim');
        parent::addAttribute('CCorrente_Usuario');
        parent::addAttribute('CCorrente_DtIncluido');
        parent::addAttribute('Pessoal_Codigo');
        parent::addAttribute('EmpresaConta_Codigo');
        parent::addAttribute('Contrato_Numero');
        parent::addAttribute('Funcao_Codigo');
    }

    public function get_pessoa_banco()
    {
        if (empty($this->pessoa_banco))
        {
            $this->pessoa_banco = new Pessoal($this->Pessoal_Codigo);
            $dados_banco = $this->pessoa_banco->get_dados_banco();
            
        }
        return $dados_banco;
    }
}