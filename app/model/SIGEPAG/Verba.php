<?php

use Adianti\Database\TRecord;

class Verba extends TRecord
{
    const TABLENAME = 'Verba';
    const PRIMARYKEY = 'Verba_Codigo';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('Verba_Codigo');
        parent::addAttribute('Verba_Descricao');
    }
}