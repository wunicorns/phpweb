<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'tb_users';
    protected $primaryKey = 'seq';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['username', 'email'];

    protected $useTimestamps = false;
    protected $createdField  = 'created_dt';
    protected $updatedField  = 'updated_dt';
    protected $deletedField  = '';

    // protected $validationRules    = [];
    // protected $validationMessages = [];
    protected $skipValidation     = true;

    protected $validationRules    = [
        'id'           => 'required|alpha_numeric_space|min_length[4]',
        'username'     => 'required|alpha_numeric_space|min_length[3]',
        'email'        => 'valid_email|is_unique[users.email]',
        'status'       => 'required|valid_email|min_length[1]|max_length[1]',
        'password'     => 'required|min_length[8]',        
        'pass_confirm' => 'required_with[password]|matches[password]',
        'role'         => 'required|min_length[4]',
    ];

    protected $validationMessages = [
        'id'        => [
            'is_unique' => 'Sorry. That id has already been taken. Please choose another.',
        ],
    ];
    
}

