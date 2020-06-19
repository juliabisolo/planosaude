<?php
/**
 * Pessoa Active Record
 * @author  <estelakrein>
 */
class Pessoa extends TRecord
{
    const TABLENAME = 'public.pessoa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cpf');
        parent::addAttribute('dt_nascimento');
        parent::addAttribute('endereco');
        parent::addAttribute('telefone');
        parent::addAttribute('cor_agenda');
        parent::addAttribute('historico');
        parent::addAttribute('ref_profissao');
        parent::addAttribute('fl_ativo');
    }

    public function getPessoa($id)
    {
        $pessoa = new Pessoa($id);

        if($pessoa)
        {
            return $pessoa;
        }
    }

    public static function validaTamanhoNome($ref_pessoa)
    {
        $pessoa = new Pessoa($ref_pessoa);

        $len = strlen($pessoa->nome);

        if($len < 120)
        {
            return true;
        }

        return false;
    }

    public static function validaTamanhoCpf($ref_pessoa)
    {
        $pessoa = new Pessoa($ref_pessoa);

        $len = strlen($pessoa->cpf);

        if($len < 15)
        {
            return true;
        }

        return false;
    }

    public static function validaTamanhoEndereco($ref_pessoa)
    {
        $pessoa = new Pessoa($ref_pessoa);

        $len = strlen($pessoa->cpf);

        if($len > 0)
        {
            return true;
        }

        return false;
    }

    public static function validaTamanhoTelefone($ref_pessoa)
    {
        $pessoa = new Pessoa($ref_pessoa);

        $len = strlen($pessoa->cpf);

        if($len < 15)
        {
            return true;
        }

        return false;
    }

    public static function validaTamanhoHistorico($ref_pessoa)
    {
        $pessoa = new Pessoa($ref_pessoa);

        $len = strlen($pessoa->cpf);

        if($len > 0)
        {
            return true;
        }

        return false;
    }

}