<?php

use Adianti\Database\TRecord;

class Pagamentos extends TRecord
{
    const TABLENAME = 'Pagamentos';
    const PRIMARYKEY = 'Pagamentos_Codigo';
    const IDPOLICY = 'serial';

    private $verba_codigo;
    private $lote_codigo;
    private $dados_conta;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('Pagamentos_Codigo');
        parent::addAttribute('Pagamentos_Valor');
        parent::addAttribute('Pagamentos_PeriodoIni');
        parent::addAttribute('Pagamentos_PeriodoFin');
        parent::addAttribute('Pagamentos_Motivo');
        parent::addAttribute('Pagamentos_Faturado');
        parent::addAttribute('Pagamentos_FaturadoSeq');
        parent::addAttribute('Pagamentos_Faturador');
        parent::addAttribute('Pagamentos_DtIncluido');
        parent::addAttribute('Pagamentos_Usuario');
        parent::addAttribute('Pagamentos_Sequencia');
        parent::addAttribute('SituacaoSis_Codigo');
        parent::addAttribute('CCorrente_Codigo');
        parent::addAttribute('Verba_Codigo');
        parent::addAttribute('FormaPgto_Codigo');
        parent::addAttribute('Lote_Codigo');
    }

    public function get_verba()
    {
        if (empty($this->verba_codigo))
        {
            $this->verba_codigo = new Verba($this->Verba_Codigo);
        }
        return $this->verba_codigo;
    }

    public function get_verba_descricao()
    {
        if (empty($this->verba_codigo))
        {
            $this->verba_codigo = new Verba($this->Verba_Codigo);
        }
        return $this->verba_codigo->Verba_Descricao;
    }

    public function get_lote()
    {
        if (empty($this->lote_codigo))
        {
            $this->lote_codigo = new Lote($this->Lote_Codigo);
        }
        return $this->lote_codigo;
    }

    public function get_lote_dtremessa()
    {
        if (empty($this->lote_codigo))
        {
            $this->lote_codigo = new Lote($this->Lote_Codigo);
        }
        return $this->lote_codigo->Lote_DtRemessa;
    }

    public function get_lote_dtpagamento()
    {
        if (empty($this->lote_codigo))
        {
            $this->lote_codigo = new Lote($this->Lote_Codigo);
        }
        return $this->lote_codigo->Lote_DtPagto;
    }

    public function get_dados_conta()
    {
        if (empty($this->dados_conta))
        {
            $this->dados_conta = new CCorrente($this->CCorrente_Codigo);
            $resultado = $this->dados_conta->get_pessoa_banco();
        }
        return $resultado;
    }
    
    public function get_lote_empresaContaCodigo()
    {
        if (empty($this->lote_codigo))
        {
            $this->lote_codigo = new Lote($this->Lote_Codigo);
        }
        return $this->lote_codigo->EmpresaConta_Codigo;
    }
}