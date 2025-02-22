<?php

use Adianti\Control\TPage;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class BuscaPessoal extends TPage
{
    private $datagrid;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('sigepag');
        $this->setActiveRecord('Pessoal');
        $this->setDefaultOrder('Pessoal_Codigo', 'asc');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        $pessoal_codigo = new TDataGridColumn('Pessoal_Codigo', 'Código', 'center', '10%');
        $pessoal_nome = new TDataGridColumn('Pessoal_Nome', 'Nome', 'left', '40%');

        $this->datagrid->addColumn($pessoal_codigo);
        $this->datagrid->addColumn($pessoal_nome);

        $this->datagrid->createModel();

        $panel = new TPanelGroup('Busca de Pessoal');
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $panel->addFooter('Total de registros: 0');

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);

        parent::add($vbox);
    }
}