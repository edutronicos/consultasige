<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class BuscaPessoal extends TPage
{
    private $form;
    private $datagrid;
    private $navigation;

    use Adianti\Base\AdiantiStandardListTrait;

    public function __construct()
    {
        parent::__construct();
        $this->setDatabase('sigepag');
        $this->setActiveRecord('Pessoal');
        $this->setDefaultOrder('Pessoal_Codigo', 'asc');
        $this->addFilterField('Pessoal_Nome', 'like', 'Pessoal_Nome');
        $this->addFilterField('Pessoal_CPF', 'like', 'Pessoal_CPF');
        $this->setLimit(50);

        $this->form = new TForm('form_busca_pessoal');

        $box = new THBox;
        $this->form->add($box);

        $campo_busca = new TEntry('c_busca');
        $campo_busca->setSize('100%');
        $campo_busca->placeholder = 'Nome ou CPF';

        $botao_busca = new TButton('busca');
        $acao_busca = new TAction([$this, 'onSearch']);
        $botao_busca->setAction($acao_busca, 'Buscar');
        $botao_busca->setImage('fa:search blue');

        $botao_limpar = new TButton('limpar');
        $acao_limpar = new TAction([$this, 'clear']);
        $botao_limpar->setAction($acao_limpar, 'Limpar');
        $botao_limpar->setImage('fa:eraser red');

        $box->add($campo_busca)->style = 'width: calc(100% - 170px); float:left; text-align:center';
        $box->add($botao_busca)->style = 'width: 80px; float:left; text-align:center';
        $box->add($botao_limpar)->style = 'width: 80px; float:left; text-align:center';

        $this->form->setFields([$campo_busca, $botao_busca, $botao_limpar]);


        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->width = '100%';

        $pessoal_codigo = new TDataGridColumn('Pessoal_Codigo', 'Código', 'center', '10%');
        $pessoal_nome = new TDataGridColumn('Pessoal_Nome', 'Nome', 'left', '60%');
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

        // $busca = new TEntry('busca');
        // $busca->placeholder = 'Nome ou CPF';
        // $busca->setSize('100%');
        // $this->datagrid->enableSearch($busca, 'Pessoal_Nome, Pessoal_CPF');  


        $this->navigation = new TPageNavigation;
        $this->navigation->enableCounters();
        $this->navigation->setAction(new TAction([$this, 'onReload']));
        $this->navigation->setWidth($this->datagrid->getWidth());

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(TPanelGroup::pack($this->form, $this->datagrid, $this->navigation));
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));

        parent::add($vbox);
    }

    function clear()
    {
        $this->form->clear();
        $this->onReload();
    }

    function onSearch($param)
    {
        $this->form->setData((object) $param);
        try {
            TTransaction::open('sigepag');
            $repository = new TRepository('Pessoal');
            $criteria = new TCriteria;
            $criteria->setProperties($param);

            if (!empty($param['c_busca'])) {
                $criteria->add(new TFilter('Pessoal_Nome', 'like', '%' . $param['c_busca'] . '%'));
                $criteria->add(new TFilter('Pessoal_CPF', 'like', '%' . $param['c_busca'] . '%'), 'OR ');
            }

            $this->setCriteria($criteria);


            $this->onReload($param);
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    function onReload($param = null)
    {
        $this->form->setData((object) $param);
        try {
            TTransaction::open('sigepag');
            $repository = new TRepository('Pessoal');
            $limit = 10;
            $criteria = new TCriteria;
            $criteria->setProperty('limit', $limit);
            $criteria->setProperties($param);

            if (!empty($param['c_busca'])) {
                $criteria->add(new TFilter('Pessoal_Nome', 'like', '%' . $param['c_busca'] . '%'));
                $criteria->add(new TFilter('Pessoal_CPF', 'like', '%' . $param['c_busca'] . '%'), 'OR ');
            }

            $this->setCriteria($criteria);

            $objects = $repository->load($criteria);
            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }
            $count_criteria = clone $criteria;
            $count_criteria->resetProperties(); // Remove limit, order, etc
            $conn = TTransaction::get();
            $sql = "SELECT COUNT(1) FROM Pessoal";
            if (!$count_criteria->isEmpty()) {
                $sql .= ' WHERE ' . $count_criteria->dump();
            }
            $stmt = $conn->prepare($sql);
            $stmt->execute($count_criteria->getPreparedVars());
            $totalRecords = $stmt->fetchColumn();

            $this->navigation->setCount($totalRecords);
            $this->navigation->setProperties($param);
            $action_p = new TDataGridAction([$this, 'onReload']);
            $action_p->setParameters($param);
            $this->navigation->setAction($action_p);
            //$this->navigation->setAction(new TAction([$this, 'onReload']));

            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    
}