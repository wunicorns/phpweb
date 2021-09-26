<?php

namespace App\Models;

use CodeIgniter\Model;

class PaperModel extends Model
{
    protected $table = 'tb_papers';

    protected $primaryKey = 'seq';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;


}

