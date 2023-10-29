<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'visits';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'url_route',
        'ip',
        'created_at',
    ];

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
     * Get all visits by route
     */
    public function getVisitsByRoute($route)
    {
        return $this->where('url_route', $route)->findAll();
    }

    /**
     * Count all visits by route
     */
    public function countVisitsByRoute($route)
    {
        return $this->where('url_route', $route)->countAllResults();
    }

    /**
     * Get visits count by date
     */
    public function countVisitsByDate($route, $startDate, $endDate)
    {
        return $this->where('url_route', $route)
                    ->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->countAllResults();
    }
    /**
     * Someone visits your url
     */
    public function newVisit($route, $ip)
    {
        $this->insert([
            'url_route' => $route,
            'ip' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
