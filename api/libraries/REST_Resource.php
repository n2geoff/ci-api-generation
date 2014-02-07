<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(__DIR__ . '/../libraries/REST_Controller.php');
require_once(__DIR__ . '/../libraries/REST_Model.php');

/**
 * REST Resource Controller
 * 
 * This abstract class ties REST_Controller to REST_Model 
 * building a Boiler-plate auto-generated foundation for 
 * RESTish/HTTP APIs.
 * 
 * Currently supports GET, POST, PUT and DELETE 
 *
 * USAGE:
 *   1. create a contoller that extends REST_Resource (v1, v2, ect...)
 *   2. create a model/ for the resource ([resource]_model.php) that extends REST_Model
 *   3. add 2 properties to your resource model
 *       - protected $_table = '';  //Database Table
 *       - protected $_id    = '';  //Database Table Primary Key
 *
 *   Try it out!
 * 
 * Application Program Interface:
 *
 * GET    /resource           return all resource records
 * GET    /resource/count     return number of resource records
 * GET    /resource/:id       return one resource record
 * POST   /resource           create new resource record  
 * POST   /resource/search    search all resource records
 * PUT    /resource/:id       update existing resource record
 * DELETE /resource/:id       delete existing resource record
 * 
 * @author Geoff Doty <n2geoff@gmail.com>
 * @since  02/06/2014
 */
abstract class REST_Resource extends REST_Controller {

    protected $_resource = NULL;
    protected $_method   = NULL;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * All the magic happens here
     *
     * @author Geoff Doty <n2geoff@gmail.com>
     *
     * @param  string $resource [description]
     * @param  array $options [description]
     *
     * @return array
     */
    public function _remap($resource, $options)
    {
        //resource requested (users, posts, ect... )
        $this->_resource = $resource;

        //http protocol request send on (get, post, put, delete)
        $this->_protocol = $this->_detect_method();  

        //method to call for request
        $method = 'resource_' . $this->_detect_method();

        //data model file name
        $model_file = __DIR__ . '/../models/' . "{$this->_resource}_model.php";

        //resources must have models or they are not resources here
        if(file_exists($model_file))
        {
            //dynamicly load resource model
            $this->load->model("api/{$this->_resource}_model", $this->_resource);      

            if (method_exists($this, $method))
            {
                return call_user_func_array(array($this, $method), $options);
            }
        } 

        log_message('error', "Requested API Resource does NOT exist: {$this->_resource}");

        //route match not found
        return $this->response(array('status' => false, 'error' => "Requested API Resource does NOT exist: {$this->_resource}"), 404);
    }

    // @todo would return all available resources and documentation
    public function index_get() 
    {
        return $this->response(FALSE, 404);
    }

    ////////////////////////////////////////////////////////////////
    /// ABSTRACTION RESOURCE OPERATIONS
    ////////////////////////////////////////////////////////////////

    /**
     * Resource GET Operations
     *
     * @author Geoff Doty <n2geoff@gmail.com>
     * @since  02/06/2014
     *
     * @param  int $id identifing route or key
     * @param  string $operation to preform on operation
     *
     * @return mixed
     */
    protected function resource_get($id = NULL, $operation = NULL)
    {
        $resources = $this->_resource;

        //capture & sanitize all query params
        $options = $this->input->get(NULL, TRUE);

        //handle /resources
        if($id === NULL) return $this->response($this->$resources->find_all($options));

        //handle /resources/count
        if($id === 'count') return $this->response($this->$resources->count());
        
        //handle /resources/:id
        if(is_numeric($id)) return $this->response($this->$resources->find_one($id, $options));

        //route match not found
        return $this->response(NULL, 404);
    }

    /**
     * Resource POST Operations
     *
     * @author Geoff Doty <n2geoff@gmail.com>
     *
     * @param  int $id primary key
     * @param  array $options 
     *
     * @return mixed
     */
    protected function resource_post($id = NULL, $options = NULL) 
    {
        $resources = $this->_resource;

        //capture & sanitize all POST data
        $data = $this->post(NULL, TRUE);

        //capture & sanitize all query params
        $options = $this->get(NULL, TRUE);

        //handle /resources/search
        if($id === 'search') return $this->response($this->$resources->search($data, $options));

        //handle /resources
        if($id === NULL) return $this->response($this->$resources->create($data));

        //route match not found
        return $this->response(NULL, 404);
    }

    /**
     * Resource PUT Operations
     *
     * @author Geoff Doty <n2geoff@gmail.com>
     *
     * @param  int $id primary key
     *
     * @return array
     */
    protected function resource_put($id) 
    {
        //short-hand model name
        $resources = $this->_resource;

        $data = $this->put(NULL, TRUE);

        //handle /resources/:id
        if(is_numeric($id)) return $this->response($this->$resources->update($id, $data));

        //route match not found
        return $this->response(NULL, 404);
    }

    protected function resource_delete($id)
    {
        //No perma-death...err...deleting
        return $this->response(FALSE, 403);
    }


    private function not_found()
    {
        return $this->response(array('status' => false, 'error' => ''), 404);
    }

}