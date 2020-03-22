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
}