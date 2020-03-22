<?php
/**
 * Pessoa Active Record
 * @author  <estelakrein>
 */
class Profissao extends TRecord
{
    const TABLENAME = 'public.profissao';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
    }
}
