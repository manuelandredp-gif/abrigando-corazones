<?php
namespace App\Models;

use App\Core\Model;

class StatsModel extends Model
{
    public function getAllStats()
    {
        $stmt = $this->db->query('SELECT * FROM estadisticas ORDER BY id ASC');
        return $stmt->fetchAll();
    }
}
