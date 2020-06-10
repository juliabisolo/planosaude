<?php
/**
 * AgendamentoForm
 * @author <juliabisolo>
 */
class AgendamentoForm extends TWindow
{
    protected $form; // form
    private static $formName = 'form_event';
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    public function __construct()
    {
        parent::__construct();
        parent::setSize(640, null);
        parent::setTitle('');
        parent::setProperty('class', 'window_modal');
        
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setProperty('style', 'margin-bottom:0');
        
        $hours = array();
        $minutes = array();

        for ($n=0; $n<24; $n++)
        {
            $hours[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }
        
        for ($n=0; $n<=55; $n+=5)
        {
            $minutes[$n] = str_pad($n, 2, '0', STR_PAD_LEFT);
        }

        $criteria_paciente = new TCriteria;
        $criteria_paciente->add(new TFilter('fl_ativo', 'is', TRUE));

        // create the form fields
        $view         = new THidden('view');
        $id           = new THidden('id');
        $color        = new THidden('color');
        $start_date   = new TDate('start_date');
        $start_hour   = new TCombo('start_hour');
        $start_minute = new TCombo('start_minute');
        $end_date     = new TDate('end_date');
        $end_hour     = new TCombo('end_hour');
        $end_minute   = new TCombo('end_minute');
        $paciente     = new TDBUniqueSearch('paciente', 'agenda', 'Pessoa', 'id', 'nome', 'nome', $criteria_paciente);
        $fl_ativo     = new TRadioGroup('fl_ativo');

        //$fl_ativo->setUseButton();
        //$fl_ativo->setLayout('horizontal');
        $fl_ativo->setValue(true);
        $fl_ativo->setBooleanMode();
        $paciente->setMinLength(1);
        $start_hour->addItems($hours);
        $start_minute->addItems($minutes);
        $end_hour->addItems($hours);
        $end_minute->addItems($minutes);

        // define the sizes
        $id->setSize(40);
        $color->setSize(100);
        $start_date->setSize(120);
        $end_date->setSize(120);
        $start_hour->setSize(60);
        $end_hour->setSize(60);
        $start_minute->setSize(60);
        $end_minute->setSize(60);
        $paciente->setSize(255);
        
        $start_hour->setChangeAction(new TAction(array($this, 'onChangeStartHour')));
        $end_hour->setChangeAction(new TAction(array($this, 'onChangeEndHour')));
        $start_date->setExitAction(new TAction(array($this, 'onChangeStartDate')));
        $end_date->setExitAction(new TAction(array($this, 'onChangeEndDate')));

        // add one row for each form field
        $this->form->addFields([$view]);
        $this->form->addFields([$id]);
        $this->form->addFields([$color]);
        $this->form->addFields([new TLabel('Início:')], [$start_date, $start_hour, ':', $start_minute]);
        $this->form->addFields([new TLabel('Fim:')], [$end_date, $end_hour, ':', $end_minute]);
        $this->form->addFields([new TLabel('Paciente:')], [$paciente]);
        $this->form->addFields([new TLabel('Ativo:')], [$fl_ativo]);
        
        $this->form->addAction( _t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addAction('Criar consulta', new TAction(array($this, 'onCriarConsulta')), 'fa:user orange');  

        parent::add($this->form);
    }

    /**
     * Executed when user leaves start hour field
     */
    public static function onChangeStartHour($param=NULL)
    {
        $obj = new stdClass;
        if (empty($param['start_minute']))
        {
            $obj->start_minute = '0';
            TForm::sendData('form_event', $obj);
        }
        
        if (empty($param['end_hour']) AND empty($param['end_minute']))
        {
            $obj->end_hour = $param['start_hour'] +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end hour field
     */
    public static function onChangeEndHour($param=NULL)
    {
        if (empty($param['end_minute']))
        {
            $obj = new stdClass;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves start date field
     */
    public static function onChangeStartDate($param=NULL)
    {
        if (empty($param['end_date']) AND !empty($param['start_date']))
        {
            $obj = new stdClass;
            $obj->end_date = $param['start_date'];
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * Executed when user leaves end date field
     */
    public static function onChangeEndDate($param=NULL)
    {
        if (empty($param['end_hour']) AND empty($param['end_minute']) AND !empty($param['start_hour']))
        {
            $obj = new stdClass;
            $obj->end_hour = min($param['start_hour'],22) +1;
            $obj->end_minute = '0';
            TForm::sendData('form_event', $obj);
        }
    }
    
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public function onSave()
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('agenda');
            
            $this->form->validate(); // form validation
            
            // get the form data into an active record Entry
            $data = $this->form->getData();
            
            $object = new Agendamento();
            $object->id = $data->id;
            $object->data_hora_inicio = $data->start_date . ' ' . str_pad($data->start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->start_minute, 2, '0', STR_PAD_LEFT) . ':00';
            $object->data_hora_fim = $data->end_date . ' ' . str_pad($data->end_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($data->end_minute, 2, '0', STR_PAD_LEFT) . ':00';

            $object->fl_ativo = $data->fl_ativo;
            $object->ref_pessoa_paciente = $data->paciente;

            $object->id = $data->id;

            if ($data->id != '')
            {
               $filtro = $object->id;
            }
            else
            {
               $filtro = 0;
            }

            $criteria = new TCriteria();
            $criteria->add(new TFilter('data_hora_inicio', '<', $object->data_hora_fim));
            $criteria->add(new TFilter('data_hora_fim', '>', $object->data_hora_inicio));
            $criteria->add(new TFilter('id', '!=', $filtro));
            $criteria->add(new TFilter('fl_ativo', '=', 'true'));

            $repository = new TRepository('Agendamento');
            $agendamentos = $repository->count($criteria);

            if(($agendamentos != 0) OR ($object->paciente == $object->medico))
            {
                new TMessage('error', 'Conflito de informações. Impossível concluir.');
            }
            else
            {
                // shows the success message
                $object->store(); // stores the object                
                $posAction = new TAction(array('AgendamentoList', 'onReload'));
                //$posAction->setParameter('view', $data->view);
                $posAction->setParameter('date', $data->start_date);
                $data->id = $object->id;
                $this->form->setData($data); // keep form data

                new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $posAction);
            }

            // parent::closeWindow($this->getId());
            TScript::create("$('.window_modal').remove();");
            
            TTransaction::close(); // close the transaction
            
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            $this->form->setData( $this->form->getData() ); // keep form data
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'agenda'
                TTransaction::open('agenda');

                $object = new Agendamento($key);
                $data = new stdClass;
                $data->id = $object->id;
                $data->color = $object->cor;
                $data->start_date = substr($object->data_hora_inicio,0,10);
                $data->start_hour = substr($object->data_hora_inicio,11,2);
                $data->start_minute = substr($object->data_hora_inicio,14,2);
                $data->end_date = substr($object->data_hora_fim,0,10);
                $data->end_hour = substr($object->data_hora_fim,11,2);
                $data->end_minute = substr($object->data_hora_fim,14,2);
                $data->fl_ativo = $object->fl_ativo;
                $data->paciente = $object->ref_pessoa_paciente;
                //$data->view = $param['view'];

                // fill the form with the active record data
                $this->form->setData($data);
                
                // close the transaction
                TTransaction::close();
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

    public function onCriarConsulta($param)
    {
        try
        {
            if (isset($param['id']))
            {
                AdiantiCoreApplication::loadPage('ConsultaForm', 'onAgendamento', $param);
                TScript::create("$('.window_modal').remove();");
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Fill form from the user selected time
     */
    public function onStartEdit($param)
    {
        $this->form->clear();
        $data = new stdClass;
        $data->view = $param['view']; // calendar view

        TQuickForm::showField('form_event', 'units');
        
        if ($param['date'])
        {
            if (strlen($param['date']) == 10)
            {
                $data->start_date = $param['date'];
                $data->end_date = $param['date'];
            }

            if (strlen($param['date']) == 19)
            {
                $data->start_date   = substr($param['date'],0,10);
                $data->start_hour   = substr($param['date'],11,2);
                $data->start_minute = substr($param['date'],14,2);
                
                $data->end_date   = substr($param['date'],0,10);
                $data->end_hour   = substr($param['date'],11,2) +1;
                $data->end_minute = substr($param['date'],14,2);
            }

            $this->form->setData( $data );
        }

        $this->form->hideField(self::$formName, 'cancelado');
    }

    //formata data BR
    public function formatDate($date)
    {
        $date = new Date($date);
        return $date->format('d/m/Y');
    }
    
    /**
     * Update event. Result of the drag and drop or resize.
     */
    public static function onUpdateEvent($param)
    {
        try
        {
            if (isset($param['id']))
            {
                // get the parameter $key
                $key=$param['id'];
                
                // open a transaction with database 'samples'
                TTransaction::open('agenda');
                
                // instantiates object CalendarEvent
                $object = new CalendarEvent($key);
                $object->start_time = str_replace('T', ' ', $param['start_time']);
                $object->end_time   = str_replace('T', ' ', $param['end_time']);
                $object->store();
                                
                // close the transaction
                TTransaction::close();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}