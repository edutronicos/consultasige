<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class BuscaPessoal extends TPage
{
    private $datagrid;
    private $navigation;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('sigepag');
        $this->setActiveRecord('Pessoal');
        $this->setDefaultOrder('Pessoal_Codigo', 'asc');
        $this->setLimit(10);

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        $pessoal_codigo = new TDataGridColumn('Pessoal_Codigo', 'Código', 'center', '10%');
        $pessoal_nome = new TDataGridColumn('Pessoal_Nome', 'Nome', 'left', '60%');
        //$banco
        $agencia = new TDataGridColumn('Pessoal_Agencia', 'Agência', 'center', '10%');
        $conta = new TDataGridColumn('Pessoal_Conta', 'Conta', 'center', '10%');
        $cpf = new TDataGridColumn('Pessoal_CPF', 'CPF', 'center', '10%');

        $this->datagrid->addColumn($pessoal_codigo);
        $this->datagrid->addColumn($pessoal_nome);
        $this->datagrid->addColumn($agencia);
        $this->datagrid->addColumn($conta);
        $this->datagrid->addColumn($cpf);

        $pagamentos_action = new TDataGridAction(['PagamentosRel', 'onReload'], ['Pessoal_Codigo' => '{Pessoal_Codigo}']);
        $this->datagrid->addAction($pagamentos_action, 'Pagamentos', 'far:file-pdf fa-fw red');

        $this->datagrid->createModel();

        $busca = new TEntry('busca');
        $busca->placeholder = 'Nome ou CPF';
        $busca->setSize('100%');
        $this->datagrid->enableSearch($busca, 'Pessoal_Nome, Pessoal_CPF');  

        $this->navigation = new TPageNavigation;
        $this->navigation->setAction(new TAction([$this, 'onReload']));
        $this->navigation->enableCounters();

        $panel = new TPanelGroup('Busca de Pessoal');
        $panel->addHeaderWidget($busca);
        $panel->add($this->datagrid)->style = 'overflow-x:auto';
        $panel->addFooter($this->navigation);

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($panel);

        parent::add($vbox);
    }

    public function onReload($param = null)
    {
        try
        {
            TTransaction::open('sigepag');

            $repository = new TRepository('Pessoal');

            $limit = 10;

            //$offset = $this->datagrid->getOffset();

            $criteria = new TCriteria;
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            //$criteria->setProperty('offset', $offset);
            $criteria->setProperty('order', $this->order);

            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();

            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();

            $count = $repository->count($criteria);

            $this->navigation->setCount($count);
            $this->navigation->setProperties($param);

            TTransaction::close();

            $this->loaded = TRUE;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}