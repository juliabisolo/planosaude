<?php
/**
 * Agendamento Active Record
 * @author  <juliabisolo>
 */
class Agendamento extends TRecord
{
    const TABLENAME = 'public.agendamento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('data_hora_inicio');
        parent::addAttribute('data_hora_fim');
        parent::addAttribute('fl_ativo');
        parent::addAttribute('ref_pessoa_paciente');
    }

    public function get_paciente()
    {
        return new Pessoa($this->paciente_id);
    }
}
