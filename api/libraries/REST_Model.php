<?php if( !defined('BASEPATH') ) exit('No direct script access allowed');

/**
 * REST Model
 *
 * This abstract class is designed to help rapidly build
 * single table resource operations.
 *
 * Pairs well with REST_Controller
 *
 * All operations that can return data will.  For example
 * calling update() on a resource will return that updated 
 * resource
 * 
 * @author Geoff Doty <n2geoff@gmail.com>
 */
abstract class REST_Model extends CI_Model {
    
    protected $_id    = NULL;
    protected $_table = NULL;

    /**
     * Find One Resource
     *
     * @author Geoff Doty <geoff.doty@assist-rx.com>
     *
     * @param  int $id primary key
     *
     * @return array
     */
    public function find_one($id)
    {
        $query = $this->db->get_where($this->_table, array($this->_id => $id));

        if($query->num_rows() == 1)
        {
            return $query->row_array();
        }
        else
        {
            return array();
        }
    }

    /**
     * Find All Resources
     *
     * @author Geoff Doty <geoff.doty@assist-rx.com>
     *
     * @param  array $params including limit & offset
     *
     * @return array
     */
    public function find_all($params = array()) 
    {
        $limit  = isset($params['limit']) ? $params['limit'] : 10;
        $offset = isset($params['offset']) && isset($params['limit']) ? $params['offset'] : NULL;

        $this->db->order_by($this->_id, 'asc');
        $query = $this->db->get($this->_table, $limit, $offset);

        if($query)
        {
            return $query->result_array();
        }

        return FALSE;

    }

    public function create($data)
    {
        if(is_array($data))
        {
            //1. does user exist
            //2. add user
            //3. return user data

            //mysql calculate date_created
            $this->db->set('date_created', 'NOW()', FALSE);

            $query = $this->db->insert($this->_table, $data);

            if($query)
            {
                $id = $this->db->insert_id();

                $this->db->where($this->_id, $id);
                $record = $this->db->get($this->_table);

                if($record)
                {
                    return $record->row_array();
                }
            }
        }

        return FALSE;
    }

    /**
     * Update Resource
     *
     * @author Geoff Doty <geoff.doty@assist-rx.com>
     *
     * @param  int $id primary key
     * @param  array $data used to update
     *
     * @return array
     */
    public function update($id, $data) 
    {
        if(is_numeric($id) && is_array($data))
        {
            $this->db->where($this->_id, $id);

            $query = $this->db->update($this->_table, $data);

            if($query)
            {
                $this->db->where($this->_id, $id);
                $record = $this->db->get($this->_table);

                if($record)
                {
                    return $record->row_array();
                }
            }
        }

        return FALSE;

    }

    /**
     * Delete Resource
     *
     * @todo currently soft deleting doesnt exist
     * 
     * @author Geoff Doty <geoff.doty@assist-rx.com>
     *
     * @return boolean
     */
    public function delete() 
    {
        return FALSE;
    }

    /**
     * Search Resource
     *
     * @author Geoff Doty <geoff.doty@assist-rx.com>
     *
     * @param  array $data key => value of fields to search agaist
     * @param  array $params including limit and offset
     *
     * @return array
     */
    public function search($data, $params = array())
    {
        $data   = NULL;
        $limit  = isset($params['limit']) ? $params['limit'] : 10;
        $offset = isset($params['offset']) && isset($params['limit']) ? $params['offset'] : NULL;

        if(is_array($data))
        {
            $this->db->like($data);
        }

        $query = $this->db->get($this->_table, $limit, $offset);

        if($query)
        {
            return $query->result_array();
        }

        return FALSE;

    }

    /**
     * Count Resource
     *
     * @author Geoff Doty <geoff.doty@assist-rx.com>
     *
     * @return int
     */
    public function count()
    {
        return $this->db->count_all($this->_table);
    }
}