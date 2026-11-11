<?php
namespace App\Models;

use App\Core\Model;

class MetaModel extends Model
{
    public function getCurrentMeta()
    {
        $stmt = $this->db->query('SELECT * FROM metas_donacion ORDER BY id DESC LIMIT 1');
        return $stmt->fetch();
    }
}
