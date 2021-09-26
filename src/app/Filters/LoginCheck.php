<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class LoginCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here

        $session = session();

        if(!$session->has('login_user')){

            // echo 'aaaaaaaaaaaaaaaaaaa<br/>';
            // print_r($session);
            // echo '<br/>bbbbbbbbbbbbbb';

            return redirect()->to('/login?=2');
        }

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
        // echo '11111111111111111111111 - after';
    }
}
