<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UrlModel;

class RestController extends ResourceController
{

    use ResponseTrait;

    protected $auth;

    public function __construct() {
        $this->auth = service('authentication');
    }

    /**
     * Return the properties of a route
     *
     * @return mixed
     */
    public function show($arg=null)
    {
        $urlModel = new UrlModel();
        $uri=$this->request->getUri();
        $route=explode('/', $uri->getPath());
        $route=$route[count($route)-1];
        $url=$urlModel->getUrlByRoute($route);
        if (!$url) {
            return $this->failNotFound('No URL found');
        }
        $data = [
            "url" => $url["url"],
        ];
        return $this->respond($data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function newUrl()
    {
        $urlModel = new UrlModel();
        $data = [
            "url" => $this->request->getPost("url"),
            "route" => bin2hex(random_bytes(3)),
        ];
        while ($urlModel->urlExists($data["route"])) {
            $data["route"] = bin2hex(random_bytes(3));
        }
        if ($urlModel->newUrl($data)) {
            return $this->respondCreated(base_url($data["route"]), 'URL created successfully');
        } else {
            return $this->fail('Failed to create URL');
        }
    }

    /**
     * Logs a user in to the system
     * 
     * @return void
     */
    public function login()
    {
        helper(['jwt_helper', 'form']);

        $rules = [
            'login' => 'required',
            'password' => 'required'
        ];
        if (!$this->validate($rules)) return $this->fail($this->validator->getErrors());
        
        $post['login'] = $this->request->getVar('login');
        $post['password'] = $this->request->getVar('password');
        
        $model = model('UserModel');
        $type = filter_var($post['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = $this->auth->validate([$type => $post['login'], 'password' => $post['password']], true);
        if (!$user) return $this->failUnauthorized('Wrong credentials');

        /****************** GENERATE TOKEN ********************/
        helper("jwt");
        $APIGroupConfig = "default";
        $cfgAPI = new \Config\APIJwt($APIGroupConfig);

        $data = array(
            "uid" => $user->id,
            "name" => $user->username,
            "email" => $user->email,
        );

        $token = newTokenJWT($cfgAPI->config(), $data);
        /****************** END TOKEN GENERATION **************/

        $response = [
            'status' => 200,
            'error' => false,
            'messages' => 'User logged In successfully',
            'user' => $user->username,
            'token' => $token
        ];

        return $this->respondCreated($response);
    }
}
