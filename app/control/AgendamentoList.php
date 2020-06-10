<?php
/**
 * AgendamentoList
 * @author <juliabisolo>
 */
class AgendamentoList extends TPage
{
    private $fullCalendar;
    
    /**
     * Page constructor
     */
    public function __construct($param)
    {
        parent::__construct();
        $this->fullCalendar = new TFullCalendar(date('Y-m-d'), 'month');
        $this->fullCalendar->setReloadAction(new TAction(array($this, 'getEvents')));
        $this->fullCalendar->setDayClickAction(new TAction(array('AgendamentoForm', 'onStartEdit')));
        $this->fullCalendar->setEventClickAction(new TAction(array('AgendamentoForm', 'onEdit')));
        $this->fullCalendar->setEventUpdateAction(new TAction(array('AgendamentoForm', 'onUpdateEvent')));
        $this->fullCalendar->disableResizing();
        parent::add( $this->fullCalendar );
    }

    public static function mostraForm($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key = $param['key'];
                list($id1, $id2) = explode("-", $key);

                $param['key'] = $id1;

                // key == c
                if($id2 == 'c')
                {
                    $window = ConsultaFormWindow::create('Consulta', 0.4, 0.56);
                }
                // key == a
                elseif($id2 == 'a')
                {
                    $window = AgendamentoForm::create('Agendamento', 0.4, 0.47);
                }
                
                $window->onEdit($param);
                $window->show();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Output events as an json
     */
    public static function getEvents($param = NULL)
    {
        $return = array();
        try
        {
            TTransaction::open('agenda');
            
            $filtro = 'true';
      
            $criteria = new TCriteria();
            $criteria->add(new TFilter('','',"NOESC: not exists (select * from consulta where ref_agendamento = agendamento.id)"));  
            $criteria->add(new TFilter('fl_ativo ', 'is', true));  

            $repository = new TRepository('Agendamento');
            $agendamentos = $repository->load($criteria);

            $registros = [];

            // guarda agendamentos
            if ($agendamentos)
            {
                foreach ($agendamentos as $agendamento)
                {
                    $registros[] = $agendamento;
                }
            }

            $repository2 = new TRepository('Consulta');
            $consultas = $repository2->load();

            // guarda consultas
            if ($consultas)
            {
                foreach ($consultas as $consulta)
                {
                    $registros[] = $consulta;
                }
            }

            if($registros)
            {
                foreach ($registros as $registro)
                {
                    $objPessoa = new Pessoa($registro->ref_pessoa_paciente);
                    $event_array = $registro->toArray();
                    $inicio = self::formatHour($event_array['data_hora_inicio']);
                    $fim = self::formatHour($event_array['data_hora_fim']);
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['data_hora_inicio']);
                    $event_array['title'] = $objPessoa->nome;
                    $event_array['color'] = $objPessoa->cor_agenda;
                
                    $popover_content = $registro->render("<b>Paciente</b>: {$objPessoa->nome} <br> <b>Início</b>: {$inicio} <br> <b>Fim</b>: {$fim}");

                    if($registro instanceof Agendamento)
                    {
                        $icone = new TElement('i');
                        $icone->style = "padding-right:4px; color:white";
                        $icone->class = "fa fa-calendar";
                        $event_array['end']   = str_replace( ' ', 'T', $event_array['data_hora_fim']);
                        $event_array['title'] = $event_array['title'] . " &nbsp; " . $icone->getContents();
                        //$event_array['id'] = $event_array['id'] . '-a';
                        $event_array['title'] = TFullCalendar::renderPopover($event_array['title'], 'Detalhes Agendamento', $popover_content);
                    }
                    else
                    {
                        $icone = new TElement('i');
                        $icone->style = "padding-right:4px; color:white";
                        $icone->class = "fa fa-check-square";
                        $event_array['end']   = str_replace( ' ', 'T', $event_array['data_hora_fim']);
                        $event_array['title'] = $event_array['title'] . " &nbsp; " . $icone->getContents();
                        //event_array['id'] = $event_array['id'] . '-c';
                        $event_array['title'] = TFullCalendar::renderPopover($event_array['title'], 'Detalhes Consulta', $popover_content);
                    }
                    
                    $return[] = $event_array;
                }
            }

            TTransaction::close();

            echo json_encode($return);
        }
        catch (Exception $e)
        {
            throw $e;
        }
    }

    //formata data BR
    public static function formatHour($date)
    {
        $timestamp = strtotime($date);
        $dateFormatted = date("H:i:s", $timestamp);

        return $dateFormatted; 
    }
    
    /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fullCalendar->setCurrentView($param['view']);
        }
        
        if (isset($param['date']))
        {
            $this->fullCalendar->setCurrentDate($param['date']);
        }
    }

    public function onSearch()
    {
        
    }
}