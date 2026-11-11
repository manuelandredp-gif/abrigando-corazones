<?php
namespace App\Models;

use App\Core\Model;

class PointModel extends Model
{
    public function getAllPoints()
    {
        $stmt = $this->db->query('SELECT * FROM puntos_acopio ORDER BY id ASC');
        return $stmt->fetchAll();
    }
}
