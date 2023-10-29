<?php

namespace App\Models;

use CodeIgniter\Model;

class UrlModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'url';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all urls
     */
    public function getAllUrls()
    {
        // Order by user_id, show the anonymous urls last
        $array = $this->asArray()
                    ->orderBy('user_id', 'ASC')
                    ->findAll();
        if (count($array) == 0) return [];
        while (!$array[0]['user_id']) {
            $temp = array_shift($array);
            array_push($array, $temp);
        }
        return $array;
    }
    
    /**
     * Get url by route
     */
    public function getUrlByRoute($route)
    {
        return $this->asArray()
                    ->where(['route' => $route])
                    ->first();
    }

    /**
     * Get urls by user
     */
    public function getUrlsByUser($user_id)
    {
        return $this->asArray()
                    ->where(['user_id' => $user_id])
                    ->findAll();
    }

    /**
     * Updates a url
     */
    public function updateUrl($route, $data) {
        $query = $this->db->table($this->table)->where(['route' => $route])->update($data);
        return $query;
    }

    /**
     * Checks if the url exists
     */
    public function urlExists($route)
    {
        $query = $this->db->table($this->table)->where(['route' => $route])->get()->getResultArray();
        return count($query) > 0;
    }

    /**
     * Checks if the url is expired
     */
    public function isUrlExpired($route)
    {
        $query = $this->db->table($this->table)->where(['route' => $route])->get()->getResultArray();
        if ($query[0]['expires'] == null)
            return false;
        else
            return $query[0]['expires'] < date('Y-m-d H:i:s');
    }

    /**
     * Checks if the url is active
     */
    public function isUrlActive($route)
    {
        $query = $this->db->table($this->table)->where(['route' => $route])->get()->getResultArray();
        return $query[0]['active'] == 1;
    }

    /**
     * Gets the created_at field
     */
    public function getCreatedAt($route)
    {
        $query = $this->db->table($this->table)->where(['route' => $route])->get()->getResultArray();
        return $query[0]['created_at'];
    }

    /**
     * Creates a new url
     */
    public function newUrl($data)
    {
        $query = $this->db->table($this->table)->insert($data);
        return $query;
    }

    /**
     * Deletes a url
     */
    public function deleteUrl($route)
    {
        $query = $this->db->table($this->table)->delete(['route' => $route]);
    }

    /**
     * Activates a url
     */
    public function activateUrl($route)
    {
        $query = $this->db->table($this->table)->set('active', 1)->where(['route' => $route])->update();
    }

    /**
     * Deactivates a url
     */
    public function deactivateUrl($route)
    {
        $query = $this->db->table($this->table)->set('active', 0)->where(['route' => $route])->update();
    }
}
