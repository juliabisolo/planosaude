<?php
/**
 * ConsultaForm Form
 * @author  <estelakrein>
 */
class ConsultaForm extends TPage
{
    protected $form; // form

    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        $this->setDatabase('agenda');   // defines the database
        $this->setActiveRecord('Consulta');     // defines the active record
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Consulta_form');
        $this->form->setFormTitle('Consulta');
        $this->form->setFieldSizes('100%');
        $this->form->setProperty('style', 'margin-bottom:0');

        $criteria_paciente = new TCriteria;
        $criteria_paciente->add(new TFilter('fl_ativo', 'is', TRUE));

        // create the form fields
        $id = new THidden('id');
        $data_hora_inicio    = new TDateTime('data_hora_inicio');
        $data_hora_fim       = new TDateTime('data_hora_fim');
        $ref_pessoa_paciente = new TDBUniqueSearch('ref_pessoa_paciente', 'agenda', 'Pessoa', 'id', 'nome', 'nome', $criteria_paciente);
        $parecer             = new TText('parecer');
        $ref_agendamento     = new THidden('ref_agendamento');

        $ref_pessoa_paciente->setMinLength(1);
        $data_hora_inicio->setMask('dd/mm/yyyy hh:ii');
        $data_hora_fim->setMask('dd/mm/yyyy hh:ii');
        $data_hora_inicio->setValue( date('d-m-Y H:i') );
        
        // define the sizes
        $id->setSize(40);
        $ref_pessoa_paciente->setSize(160);
        $parecer->setSize(600);

        // add one row for each form field
        $this->form->addFields([$id]);

        $this->form->addFields([$ref_agendamento]);

        $row = $this->form->addFields( [new TLabel('Início:'), $data_hora_inicio] );
        $row->layout = ['col-sm-3']; //comprimento do campo, setSize não funcionou

        $row = $this->form->addFields( [new TLabel('Fim:'), $data_hora_fim] );
        $row->layout = ['col-sm-3']; //comprimento do campo, setSize não funcionou

        $row = $this->form->addFields( [new TLabel('Paciente:'), $ref_pessoa_paciente] );
        $row->layout = ['col-sm-8']; //comprimento do campo, setSize não funcionou

        $row = $this->form->addFields( [new TLabel('Receituário:'), $parecer]);
        
        $this->form->addAction( _t('Save'),   new TAction(array($this, 'onSave')),   'fa:save green');
        $this->form->addAction(_t('Cancel'), new TAction(array('ConsultaList', 'onReload')), 'far:times-circle red');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave($param)
    {
        try
        {
            // open a transaction with database 'samples'
            TTransaction::open('agenda');
            
            $this->form->validate(); // form validation
            
            // get the form data into an active record Entry
            $data = $this->form->getData();

            $object                      = new Consulta();
            $object->id                  = $data->id;
            $object->data_hora_inicio    = $data->data_hora_inicio;
            $object->data_hora_fim       = $data->data_hora_fim;
            $object->ref_pessoa_paciente = $data->ref_pessoa_paciente;
            $object->parecer             = $data->parecer;
            $object->ref_agendamento     = $data->ref_agendamento;

            if($object->data_hora_inicio > $object->data_hora_fim)
            {
                ConsultaForm::alertDatasConsulta($param);
                return;
            }

            $object->store(); // stores the object
            $data->id = $object->id;
            $this->form->setData($data); // keep form data
            
            TTransaction::close(); // close the transaction
            $posAction = new TAction(array('ConsultaList', 'onReload'));
            
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $posAction);
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

    public static function alertDatasConsulta($param)
    {       
        // shows a dialog to the user
        new TMessage('error', 'A data inicial da consulta deve ser menor do que a data final');
    }

    public function formatDateEdit($date)
    {
        $timestamp = strtotime($date);
        $dateFormatted = date("d/m/Y H:i", $timestamp);

        return $dateFormatted; 
    }

    public function onAgendamento( $param )
    {
        try
        {
            if (isset($param['id']))
            {
                $key = $param['id'];  // get the parameter $key
                TTransaction::open('agenda'); // open a transaction
                
                $agendamento = new Agendamento($key); // instantiates the Active Record

                $object = new stdClass;
                $object->ref_pessoa_paciente = $agendamento->ref_pessoa_paciente;
                $object->ref_agendamento = $agendamento->id;

                $start_date = substr($agendamento->data_hora_inicio,0,10);
                $start_hour = substr($agendamento->data_hora_inicio,11,2);
                $start_minute = substr($agendamento->data_hora_inicio,14,2);
                $object->data_hora_inicio = $start_date . ' ' . str_pad($start_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($start_minute, 2, '0', STR_PAD_LEFT) . ':00';
                $dateFormatted = $this->formatDateEdit($object->data_hora_inicio);
                $object->data_hora_inicio = $dateFormatted;

                $end_date = substr($agendamento->data_hora_fim,0,10);
                $end_hour = substr($agendamento->data_hora_fim,11,2);
                $end_minute = substr($agendamento->data_hora_fim,14,2);
                $object->data_hora_fim = $end_date . ' ' . str_pad($end_hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($end_minute, 2, '0', STR_PAD_LEFT) . ':00';
                $dateFormatted = $this->formatDateEdit($object->data_hora_fim);
                $object->data_hora_fim = $dateFormatted;

                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
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
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
         try
        {
            if (empty($this->database))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (empty($this->activeRecord))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database
                TTransaction::open($this->database);
                
                $class = $this->activeRecord;
                
                // instantiates object
                $object = new $class($key);

                $dateFormatted = $this->formatDateEdit($object->data_hora_inicio);
                $object->data_hora_inicio = $dateFormatted;
                
                $dateFormatted = $this->formatDateEdit($object->data_hora_fim);
                $object->data_hora_fim = $dateFormatted;


                // fill the form with the active record data
                $this->form->setData($object);
                // close the transaction
                TTransaction::close();
                
                return $object;
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
}
