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

    public static function getTotal()
    {
        $profissoes = Consulta::where('1', '=', '1')->load();

        $count = 0;
        foreach ($profissoes as $profissao) 
        {
            $count++;
        }

        return $count;
    }

    public static function validaTamanho($ref_profissao)
    {
        $profissao = new Profissao($ref_profissao);

        $len = strlen($profissao->descricao);

        if($len < 120)
        {
            return true;
        }

        return false;
    }
}
