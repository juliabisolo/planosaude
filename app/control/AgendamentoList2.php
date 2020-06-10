<?php
/**
 * AgendamentoList2 Listing
 * @author  <juliabisolo>
 */
class AgendamentoList2 extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_Agendamento');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('AGENDAMENTOS');

        // create the form fields
        $id = new THidden('id');
        $ref_pessoa_paciente = new TDBUniqueSearch('ref_pessoa_paciente', 'agenda', 'Pessoa', 'id', 'nome', 'nome');

        $ref_pessoa_paciente->setMinLength(1);

        // add the fields
        $this->form->addQuickField('Id', $id,  200);
        $this->form->addQuickField('Paciente', $ref_pessoa_paciente,  200);
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Agendamento_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('AgendamentoList', 'onReload')), 'fa:plus-circle green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(400);  

        // creates the datagrid columns
        $column_data_hora_inicio    = new TDataGridColumn('data_hora_inicio',    'Início',   'left');
        $column_data_hora_fim       = new TDataGridColumn('data_hora_fim',       'Fim',      'left');
        $column_ref_pessoa_paciente = new TDataGridColumn('ref_pessoa_paciente', 'Paciente', 'left');
        $column_fl_ativo            = new TDataGridColumn('fl_ativo',            'Ativo',    'left');

        $column_ref_pessoa_paciente->setTransformer([$this, 'transformerPaciente']);

        $column_fl_ativo->setTransformer(function($fl_ativo)
        {
            $icone = new TElement('i');
            
            $title = 'Inativo';
            $class = "ban";
            $icone->style = "padding-right:4px; color:red";

            if($fl_ativo)
            {
                $title = 'Ativo';    
                $class = "check";
                $icone->style = "padding-right:4px; color:green";
            }

            $icone->title = $title;
            $icone->class = "fa fa-{$class} fa-fw";

            return $icone;
        });

        $column_data_hora_inicio->setTransformer(array($this, 'formatDate'));
        $column_data_hora_fim->setTransformer(array($this, 'formatDate'));

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_ref_pessoa_paciente);
        $this->datagrid->addColumn($column_data_hora_inicio);
        $this->datagrid->addColumn($column_data_hora_fim);
        $this->datagrid->addColumn($column_fl_ativo);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('AgendamentoForm', 'onEdit'));
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:edit blue');
        $action_edit->setField('id');  

        //create DESATIVAR action
        $action_desativar = new TDataGridAction(array($this, 'onDesativar'));
        $action_desativar->setUseButton(TRUE);
        $action_desativar->setButtonClass('btn btn-default');
        $action_desativar->setLabel(('Desativar'));
        $action_desativar->setImage('fa:user-times red');
        $action_desativar->setField('id');
        $action_desativar->setDisplayCondition( array($this, 'displayDesativa') );

        //create ATIVAR action
        $action_ativar = new TDataGridAction(array($this, 'onAtivar'));
        $action_ativar->setUseButton(TRUE);
        $action_ativar->setButtonClass('btn btn-default');
        $action_ativar->setLabel(('Ativar'));
        $action_ativar->setImage('fa:user green');
        $action_ativar->setField('id');
        $action_ativar->setDisplayCondition( array($this, 'displayAtiva') );

        $action_group = new TDataGridActionGroup('Ações', 'bs:th');        
        $action_group->addAction($action_edit);
        $action_group->addAction($action_desativar);
        $action_group->addAction($action_ativar);
        
        $this->datagrid->addActionGroup($action_group);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }

    public function transformerPaciente($paciente_id, $consulta, $row)
    {
        if($consulta->ref_pessoa_paciente)
        {
            $objPessoa = new Pessoa($consulta->ref_pessoa_paciente);
            return $objPessoa->nome;
        }

        return '';
    }

    //formata data BR
    public function formatDate($date, $object)
    {
        $dt = new DateTime($date);
        return $dt->format('d/m/Y H:i');
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('AgendamentoList_filter_ref_pessoa_paciente',   NULL);

        if (isset($data->ref_pessoa_paciente) AND ($data->ref_pessoa_paciente)) 
        {
            $filter = new TFilter('ref_pessoa_paciente', '=', "%{$data->ref_pessoa_paciente}%"); // create the filter
            TSession::setValue('AgendamentoList_filter_ref_pessoa_paciente', $filter); // stores the filter in the session
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Agendamento_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'agenda_julia'
            TTransaction::open('agenda');
            
            // creates a repository for Pessoa
            $repository = new TRepository('Agendamento');
            
            $limit = 10;

            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('AgendamentoList_filter_ref_pessoa_paciente')) {
                $criteria->add(TSession::getValue('AgendamentoList_filter_ref_pessoa_paciente')); // add the session filter
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

      public function onDesativar($param)
    {
        $action = new TAction(array(__CLASS__, 'desativa'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion('Você tem certeza que deseja desativar este agendamento?', $action);
    }

    public function desativa($param)
    {
        TTransaction::open('agenda');
        $agendamento = new Agendamento($param['id']);
        $agendamento->fl_ativo = FALSE;
        $agendamento->store();
        AdiantiCoreApplication::gotoPage('AgendamentoList2');
        TTransaction::close();
    }

    public static function onAtivar($param)
    {
        $action = new TAction(array(__CLASS__, 'ativa'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion('Você tem certeza que deseja ativar este agendamento?', $action);
    }

    public function ativa($param)
    {
        TTransaction::open('agenda');
        $agendamento = new Agendamento($param['id']);
        $agendamento->fl_ativo = TRUE;
        $agendamento->store();
        AdiantiCoreApplication::gotoPage('AgendamentoList2');
        TTransaction::close();
    }

    public function displayAtiva($agendamento)
    {
        if($agendamento->fl_ativo)
        {
            return false;
        }
        return true;
    }

    public function displayDesativa($agendamento)
    {
        if(!$agendamento->fl_ativo)
        {
            return false;
        }
        return true;
    }
    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
