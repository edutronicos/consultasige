<?php

use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Form\TDate;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class PagamentosRel extends TWindow
{
    private $datagrid;

    use Adianti\Base\AdiantiStandardFormListTrait;

    function __construct()
    {
        parent::__construct();
        parent::setModal(TRUE);
        parent::removePadding();
        parent::setSize(1080, null);
        parent::setTitle('Relatório de Pagamentos');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        $this->setDatabase('sigepag');
        $this->setActiveRecord('Pagamentos');
        $this->setDefaultOrder('Pagamentos_Codigo', 'asc');

        $this->datagrid->width = '100%';

        $remessa = new TDataGridColumn('lote_dtremessa', 'Remessa', 'center', '5%');
        $pagamento = new TDataGridColumn('lote_dtpagamento', 'Pagamento', 'center', '5%');
        $periodo_ini = new TDataGridColumn('Pagamentos_PeriodoIni', 'Período Inicial', 'center', '5%');
        $periodo_fin = new TDataGridColumn('Pagamentos_PeriodoFin', 'Período Final', 'center', '5%');
        $valor = new TDataGridColumn('Pagamentos_Valor', 'Valor', 'left', '5%');
        $verba = new TDataGridColumn('verba_descricao', 'Verba', 'left', '10%');
        $ccorrente_codigo = new TDataGridColumn('dados_conta', 'Conta Corrente', 'center', '20%');

        $remessa->setTransformer(function ($value) {
            return TDate::date2br($value);
        });

        $pagamento->setTransformer(function ($value) {
            return TDate::date2br($value);
        });

        $valor->setTransformer(function ($value) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        });

        $periodo_fin->setTransformer(function ($value) {
            return TDate::date2br($value);
        });

        $periodo_ini->setTransformer(function ($value) {
            return TDate::date2br($value);
        });

        $this->datagrid->addColumn($remessa);
        $this->datagrid->addColumn($pagamento);
        $this->datagrid->addColumn($valor);        
        $this->datagrid->addColumn($ccorrente_codigo);
        $this->datagrid->addColumn($verba);
        $this->datagrid->addColumn($periodo_ini);
        $this->datagrid->addColumn($periodo_fin);

        $this->datagrid->createModel();

        parent::add($this->datagrid);
    }

    public function onReload($param = null)
    {
        $this->datagrid->clear();

        TTransaction::open('sigepag');

        $repository = new TRepository('CCorrente');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('Pessoal_Codigo', '=', $param['Pessoal_Codigo']));
        $ccorrentes = $repository->load($criteria);
        
        

        foreach ($ccorrentes as $ccorrente) {
            //$this->datagrid->addItem($ccorrente);

            $repository = new TRepository('Pagamentos');
            $criteria = new TCriteria;
            $criteria->add(new TFilter('CCorrente_Codigo', '=', $ccorrente->CCorrente_Codigo));
            $pagamentos = $repository->load($criteria);

            foreach ($pagamentos as $pagamento) {
                $this->datagrid->addItem($pagamento);
            }
        }




        TTransaction::close();
    }
}