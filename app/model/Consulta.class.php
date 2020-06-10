<?php
/**
 * Consulta Active Record
 * @author  <estelakrein>
 */
class Consulta extends TRecord
{
    const TABLENAME = 'public.consulta';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    private $paciente;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('parecer');
        parent::addAttribute('data_hora_inicio');
        parent::addAttribute('data_hora_fim');
        parent::addAttribute('ref_agendamento');
        parent::addAttribute('ref_pessoa_paciente');
    }

    public function get_nome()
    {
        $pessoa = new Pessoa($this->ref_pessoa_paciente);
        return new $pessoa->nome;
    }
}
