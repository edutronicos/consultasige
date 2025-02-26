<?php

use Adianti\Control\TAction;
use Adianti\Control\TWindow;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Util\TXMLBreadCrumb;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class PagamentosRel extends TWindow
{
    private $datagrid;

    use Adianti\Base\AdiantiStandardListExportTrait;
    use \Adianti\Base\AdiantiStandardListTrait;

    function __construct()
    {
        parent::__construct();
        parent::setModal(TRUE);
        parent::removePadding();
        parent::setSize(1200, null);
        parent::setTitle('Relatório de Pagamentos');

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);

        $this->setDatabase('sigepag');
        $this->setActiveRecord('Pagamentos');
        //$this->setDefaultOrder('lote_dtpagamento', 'desc');

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

        $painel = new TPanelGroup('Relatório de Pagamentos');
        $painel->add($this->datagrid);

        $painel->addHeaderActionLink( 'PDF', new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static' => '1']), 'far:file-pdf red' );

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        //$vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($painel);

        parent::add($vbox);
    }

    public function onReload($param = null)
    {
        $this->datagrid->clear();

        TTransaction::open('sigepag');

        $repository = new TRepository('CCorrente');
        $criteria = new TCriteria;
        // if (TSession::getValue('pessoal_codigo')) {
        //     $criteria->add(new TFilter('Pessoal_Codigo', '=', TSession::getValue('pessoal_codigo')));
        // } else {
        //     $criteria->add(new TFilter('Pessoal_Codigo', '=', $param['Pessoal_Codigo']));
        //     TSession::setValue('pessoal_codigo', $param['Pessoal_Codigo']);
        // }

        if(isset($param['Pessoal_Codigo'])) {
            $criteria->add(new TFilter('Pessoal_Codigo', '=', $param['Pessoal_Codigo']));
            TSession::setValue('pessoal_codigo', $param['Pessoal_Codigo']);
        } else {
            $criteria->add(new TFilter('Pessoal_Codigo', '=', TSession::getValue('pessoal_codigo')));
        }
        
        $ccorrentes = $repository->load($criteria);
        
        

        foreach ($ccorrentes as $ccorrente) {
            //$this->datagrid->addItem($ccorrente);

            $repository = new TRepository('Pagamentos');
            $criteria = new TCriteria;
            $criteria->setProperty('order', 'Pagamentos_Codigo asc');
            $criteria->add(new TFilter('CCorrente_Codigo', '=', $ccorrente->CCorrente_Codigo));
            $pagamentos = $repository->load($criteria);

            foreach ($pagamentos as $pagamento) {
                if ($pagamento->lote_empresaContaCodigo != '59' || $pagamento->lote_empresaCodigo != '13') {
                    $this->datagrid->addItem($pagamento);
                }
                
            }
        }

        TTransaction::close();
    }

    public function onExportPDF($param)
    {
        try
        {
            // Remove limites e recarrega o datagrid
            $this->limit = 0; 
            $this->onReload($param);
    
            // Agora sim o datagrid tem todos os itens
            $items = $this->datagrid->getOutputData();
            TSession::delValue('pessoal_codigo');
            if (!$items)
            {
                new TMessage('info', 'Não há dados para exportar');
                return;
            }

            $headerRow = array_shift($items);
    
            $widths = [70, 70, 80, 300, 180, 90, 90]; // Ajuste conforme sua necessidade
            $pdf    = new TTableWriterPDF($widths, 'L', 'A3');

            // Cria alguns estilos simples
            $pdf->addStyle('title', 'Arial', '10', 'B', '#ffffff', '#7C8EA9');
            $pdf->addStyle('datap', 'Arial', '10', '',  '#000000', '#ffffff');

            // Cabeçalho
            $pdf->addRow();
            foreach ($headerRow as $titleCell) {
                $pdf->addCell($titleCell, 'center', 'title');
            }

            foreach ($items as $row) {
                // Cada $row é outro array com os valores das colunas
                $pdf->addRow();
                foreach ($row as $cell) {
                    $pdf->addCell($cell, 'left', 'datap');
                }
            }

            // Gera e abre o arquivo
            $tempFile = 'tmp/'.uniqid().'.pdf';
            $pdf->save($tempFile);
           
            $window = TWindow::create('Export', 0.8, 0.8);
            $object = new TElement('object');
            $object->data  = $tempFile;
            $object->type  = 'application/pdf';
            $object->style = "width: 100%; height:calc(100% - 10px)";
            $object->add('O navegador não suporta a exibição deste conteúdo, <a style="color:#007bff;" target=_newwindow href="'.$object->data.'"> clique aqui para baixar</a>...');
            
            $window->add($object);
            $window->show();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

}