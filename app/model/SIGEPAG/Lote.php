<?php

use Adianti\Database\TRecord;

class Lote extends TRecord
{
    const TABLENAME = 'Lote';
    const PRIMARYKEY = 'Lote_Codigo';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('Lote_Codigo');
        parent::addAttribute('Lote_DtPagto');
        parent::addAttribute('Lote_Valor');
        parent::addAttribute('Lote_Arquivo');
        parent::addAttribute('Lote_DtRemessa');
        parent::addAttribute('Lote_NPagto');
        parent::addAttribute('Lote_Nlote');
        parent::addAttribute('Banco_Codigo');
        parent::addAttribute('FormaPgto_Codigo');
        parent::addAttribute('Lote_Liberado');
        parent::addAttribute('Usuarios_Codigo');
        parent::addAttribute('Empresa_Codigo');
        parent::addAttribute('EmpresaConta_Codigo');
        parent::addAttribute('Cliente_Codigo');
    }
}