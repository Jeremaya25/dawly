<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UrlModel;
use Myth\Auth\Entities\User;

class RestPrivateController extends ResourceController
{

    use ResponseTrait;

    protected $auth;
    protected $authorize;

    public function __construct() {
        $this->config = config('Auth');
        $this->auth = service('authentication');
        $this->authorize = service('authorization');
    }

    /**
     * Returns the properties of a route
     * 
     * @return mixed
     */
    public function show($arg = null) {
        $urlModel = new UrlModel();

        $uri=$this->request->getUri();
        $route=explode('/', $uri->getPath());
        $route=$route[count($route)-1];
        $url = $urlModel->getUrlByRoute($route);

        if (!$url) {
            return $this->failNotFound('No URL found');
        }
        else if (in_array("user_id", $url)) {
            if ($url["user_id"] != $userId
            || !$this->authorize->hasPermission('manage.urls', $userId))
                return $this->failForbidden("You don't have permission to access this URL");
        }
        $data = [
            "url" => $url["url"],
            "route" => $url["route"],
            "shortname" => $url["shortname"],
            "description" => $url["description"],
            "active" => $url["active"],
            "user_id" => $url["user_id"],
            "expires" => $url["expires"],
            "created_at" => $url["created_at"],
        ];
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No URL found');
        }
    }

    /**
     * Creates a new url object
     * 
     * @return mixed
     */
    public function newUrl() {
        $urlModel = model('UrlModel');
        $token_data = json_decode($this->request->header("token-data")->getValue());
        
        $this->checkPerms($token_data, 'urls.add');
        
        $data = [
            "url" => $this->request->getPost("url"),
            "route" => $this->request->getPost("route") || bin2hex(random_bytes(3)),
            "shortname" => $this->request->getPost("shortname"),
            "description" => $this->request->getPost("description"),
            "active" => $this->request->getPost("active"),
            "user_id" => $token_data->uid,
            "expires" => $this->request->getPost("expires"),
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
     * Updates a url object
     * 
     * @return mixed
     */
    public function updateUrl($args = null) {
        $urlModel = model('UrlModel');
        
        $uri=$this->request->getUri();
        $route=explode('/', $uri->getPath());
        $route=$route[count($route)-1];

        $token_data = json_decode($this->request->header("token-data")->getValue());
        
        $this->checkPerms($token_data, 'urls.update');

        $data = [
            "shortname" => $this->request->getPost("shortname"),
            "description" => $this->request->getPost("description"),
            "active" => $this->request->getPost("active"),
        ];

        if ($urlModel->updateUrl($route, $data)) {
            return $this->respondUpdated($urlModel->getUrlByRoute($route), 'URL updated successfully');
        } else {
            return $this->fail('Failed to update URL');
        }
    }

    /**
     * Gets all users
     * 
     * @return mixed
     */
    public function users() {
        $token_data = json_decode($this->request->header("token-data")->getValue());
        
        $this->checkPerms($token_data, 'users.manage');

        $userModel = model('UserModel');
        $users = $userModel->getAllUsers();

        if ($users) {
            return $this->respond($users);
        } else {
            return $this->failNotFound('No users found');
        }
    }

    /**
     * Gets selected user or your user
     * 
     * @return mixed
     */
    public function user($user=null) {
        $token_data = json_decode($this->request->header("token-data")->getValue());
        $userModel = model('UserModel');
        
        if ($user == null) {
            $user = $userModel->getUserById($token_data->uid);
        }
        else {
            $this->checkPerms($token_data, 'users.manage');
            $user = $userModel->getUserById($user);
        }

        if ($user) {
            return $this->respond($user);
        } else {
            return $this->failNotFound('The user does not exist');
        }
    }

    /**
     * Validates a user with it's credentials
     * 
     * @return mixed
     */
    public function validateUser() {
        $token_data = json_decode($this->request->header("token-data")->getValue());
        $userModel = model('UserModel');

        $this->checkPerms($token_data, 'users.manage');
        
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
        $data = [
            'status' => 200,
            'error' => false,
            'messages' => 'User is valid',
            'user' => $user
        ];
        return $this->respond($data);
    }

    /**
     * Creates a new user
     * 
     * @return mixed
     */
    public function newUser() {
        $token_data = json_decode($this->request->header("token-data")->getValue());
        $userModel = model('UserModel');

        $this->checkPerms($token_data, 'users.add');

        $rules = config('Validation')->registrationRules ?? [
            'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }
        
        $rules = [
            'password'     => 'required|strong_password',
        ];

        // Save the user
        $allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
        $user              = new User($this->request->getPost($allowedPostFields));

        $user->generateActivateHash();

        // Ensure default group gets assigned if set
        if (! empty($this->config->defaultUserGroup)) {
            $users = model("UserModel")->withGroup($this->config->defaultUserGroup);
        }

        if (! $users->save($user)) {
            return $this->fail($users->errors());
        }

        return $this->respondCreated($user, 'User created successfully, waiting for activation');
    }
    
    /**
     * Updates a user
     * 
     * @param int $user_id
     * @return mixed
     */
    public function updateUser ($user_id) {
        $token_data = json_decode($this->request->header("token-data")->getValue());
        $userModel = model('UserModel');

        if ($user_id != $token_data->uid)
            $this->checkPerms($token_data, 'users.manage');
        
        $rules = config('Validation')->registrationRules ?? [
            'email'   => 'valid_email|is_unique[users.email]',
            'username' => 'is_unique[users.username]'
        ];

        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $data = [
            "email" => $this->request->getPost("email"),
            "username" => $this->request->getPost("username"),
        ];

        if ($userModel->updateUser($user_id, $data)) {
            return $this->respondUpdated($userModel->getUserById($user_id), 'User updated successfully');
        } else {
            return $this->fail('Failed to update user');
        }
    }

    /** 
     * Gets current permissions of a user
     * 
     * @param int $user_id
     * @return mixed
     */
    public function userRoles($user_id) {
        $token_data = json_decode($this->request->header("token-data")->getValue());

        $this->checkPerms($token_data, 'users.manage');
        
        $permissions = $this->authorize->permissions();
        $userPerms = [];
        for ($i=0; $i<count($permissions); $i++) {
            if ($this->authorize->hasPermission($permissions[$i]['id'], $user_id)) {
                array_push($userPerms, $permissions[$i]);
            }
        }

        if ($userPerms != []) {
            return $this->respond($userPerms);
        } else {
            return $this->failNotFound('The user does not exist or does not have any permissions');
        }
    }

    /**
     * Checks if a user is in a group
     * 
     * @param int $user_id
     * @param string $group
     */
    public function userInGroup($user_id, $group) {
        $token_data = json_decode($this->request->header("token-data")->getValue());

        $this->checkPerms($token_data, 'users.manage');
        
        if ($this->authorize->inGroup($group, $user_id)) {
            return $this->respond(true);
        } else {
            return $this->respond(false);
        }
    }

    /**
     * Checks if a user is authorized to do an action
     * 
     * @param object $token_data
     * @param string $perm
     * @return mixed
     */
    private function checkPerms($token_data, $perm = null) {
        $userId = $token_data->uid;

        if (!$userId) {
            return $this->failUnauthorized("Invalid token");
        }
        else if ($perm != null && $this->authorize->hasPermission($perm, $userId)) {
            return $this->failForbidden("You don't have '$perm' permission");
        }
    }
}
