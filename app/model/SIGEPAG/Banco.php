<?php

use Adianti\Database\TRecord;

class Banco extends TRecord
{
    const TABLENAME = 'Banco';
    const PRIMARYKEY = 'Banco_Codigo';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('Banco_Codigo');
        parent::addAttribute('Banco_Descricao');
    }
}