<?php
/**
 * PessoaList Listing
 * @author  <estelakrein>
 */
class ProfissaoList extends TPage
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
        $this->form = new TQuickForm('form_search_Profissao');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('PROFISSÕES');

        // create the form fields
        $descricao = new TEntry('descricao');


        // add the fields
        $this->form->addQuickField('Descrição', $descricao,  200);

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Profissao_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ProfissaoForm', 'onEdit')), 'fa:plus-circle green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(400);  

        // creates the datagrid columns
        $column_descricao = new TDataGridColumn('descricao',     'Descrição',          'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_descricao);       
		
		// create EDIT action
        $action_edit = new TDataGridAction(array('ProfissaoForm', 'onEdit'));
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:edit blue');
        $action_edit->setField('id');
		
        //create DELETE action
        $action_delete = new TDataGridAction(array($this, 'onDelete'));
        $action_delete->setUseButton(TRUE);
        $action_delete->setButtonClass('btn btn-default');
        $action_delete->setLabel(('Excluir'));
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
        TSession::setValue('ProfissaoList_filter_descricao',   NULL);

        if (isset($data->descricao) AND ($data->descricao)) 
        {
            $filter = new TFilter('descricao', 'ilike', "%{$data->descricao}%"); // create the filter
            TSession::setValue('ProfissaoList_filter_descricao', $filter); // stores the filter in the session
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Profissao_filter_data', $data);
        
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
            $repository = new TRepository('Profissao');
            
            $limit = 10;

            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            if (TSession::getValue('ProfissaoList_filter_descricao')) {
                $criteria->add(TSession::getValue('ProfissaoList_filter_descricao')); // add the session filter
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
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion('Você tem certeza que deseja excluir esta profissão?', $action);
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
            $object = new Profissao($key, FALSE);
            
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
            new TMessage('error', 'Não foi possível excluir o registro, pois há usuários vinculados à ele.');
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
}
