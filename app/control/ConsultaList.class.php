<?php
/**
 * PessoaList Listing
 * @author  <estelakrein>
 */
class ConsultaList extends TPage
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
        $this->form = new TQuickForm('form_search_Consulta');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('CONSULTAS');

        // create the form fields
        $ref_pessoa_paciente = new TDBUniqueSearch('ref_pessoa_paciente', 'agenda', 'Pessoa', 'id', 'nome');
        $ref_pessoa_paciente->setMinLength(1);

        // add the fields
        $this->form->addQuickField('Paciente', $ref_pessoa_paciente,  200);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Consulta_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ConsultaForm', 'onEdit')), 'fa:plus-circle green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_data_hora_inicio = new TDataGridColumn('data_hora_inicio', 'Data/Hora Início', 'left');
        $column_data_hora_fim = new TDataGridColumn('data_hora_fim', 'Data/Hora Fim', 'left');
        $column_ref_pessoa_paciente = new TDataGridColumn('ref_pessoa_paciente', 'Paciente', 'left');
        $column_ref_pessoa_paciente->setTransformer([$this, 'transformerPaciente']);
        $column_data_hora_inicio->setTransformer(array($this, 'formatDate'));
        $column_data_hora_fim->setTransformer(array($this, 'formatDate'));

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_data_hora_inicio);
        $this->datagrid->addColumn($column_data_hora_fim);
        $this->datagrid->addColumn($column_ref_pessoa_paciente);
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ConsultaForm', 'onEdit'));
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:edit blue');
        $action_edit->setField('id');  

        // create DELETE action
        $action_delete = new TDataGridAction(array($this, 'onDelete'));
        $action_delete->setButtonClass('btn btn-default');
        $action_delete->setLabel(_t('Delete'));
        $action_delete->setImage('fa:trash-alt red');
        $action_delete->setField('id');
        
        $action_group = new TDataGridActionGroup('Ações', 'bs:th');
        $action_group->addAction($action_edit);
        $action_group->addAction($action_delete);
        
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
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        // clear session filters
        TSession::setValue('ConsultaList_filter_paciente',   NULL);

        if (isset($data->ref_pessoa_paciente) AND ($data->ref_pessoa_paciente)) {
            $filter = new TFilter('ref_pessoa_paciente', '=', "$data->ref_pessoa_paciente"); // create the filter
            TSession::setValue('ConsultaList_filter_paciente',   $filter); // stores the filter in the session
        }

        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Consulta_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
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
        $date = new DateTime($date);
        return $date->format('d/m/Y H:i:s');
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'agenda'
            TTransaction::open('agenda');
            
            // creates a repository for Pessoa
            $repository = new TRepository('Consulta');
            
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
            

            if (TSession::getValue('ConsultaList_filter_paciente')) {
                $criteria->add(TSession::getValue('ConsultaList_filter_paciente')); // add the session filter
            }


            if (TSession::getValue('ConsultaList_filter_medico')) {
                $criteria->add(TSession::getValue('ConsultaList_filter_medico')); // add the session filter
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

    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database
            TTransaction::open('agenda');
            
            // instantiates object
            $object = new Consulta($key, FALSE);
            
            // deletes the object from the database
            $object->delete();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload( $param );
            // shows the success message
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
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
