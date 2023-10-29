<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UrlModel;
use App\Models\UserModel;
use App\Models\VisitModel;

class PrivateController extends BaseController {
    
    public function index()
    {
        return view('private/main_url');
    }

    public function dashboard()
    {
        $userModel = new UserModel();
        $urlModel = new UrlModel();
        $visitModel = new VisitModel();
        if (has_permission('urls.manage')) {
            $data['userModel'] = $userModel;
            $data['urls'] = $urlModel->getAllUrls();
        }
        else {
            $data['urls'] = $urlModel->getUrlsByUser(user_id());
        }
        $data['visitModel'] = $visitModel;
        return view('private/dashboard', $data);
    }

    public function statistics($url_route)
    {
        $urlModel = new UrlModel();
        $visitsModel = new VisitModel();
        $data['url'] = $urlModel->getUrlByRoute($url_route);
        $data['visits'] = $visitsModel->getVisitsByRoute($url_route);
        $data['absoluteStartDate'] = $urlModel->getCreatedAt($url_route);
        return view('private/statistics', $data);
    }

    public function deleteUrl($route)
    {
        $urlModel = new UrlModel();
        $urlModel->deleteUrl($route);
        return redirect()->to('/private/dashboard');
    }
    
    public function activateUrl($route)
    {
        $urlModel = new UrlModel();
        $urlModel->activateUrl($route);
        return redirect()->to('/private/dashboard');
    }
    
    public function deactivateUrl($route)
    {
        $urlModel = new UrlModel();
        $urlModel->deactivateUrl($route);
        return redirect()->to('/private/dashboard');
    }
    
    public function usersDashboard() {
        $userModel = new UserModel();
        $data = [
            'users' => $userModel->getAllUsers(),
            'userModel' => $userModel
        ];
    
        return view('private/users_dashboard', $data);
    }

    public function deleteUser($id) {
        $userModel = new UserModel();
        $userModel->deleteUser($id);
        return redirect()->to('/private/users');
    }

    public function activateUser($id) {
        $userModel = new UserModel();
        $userModel->activateUser($id);
        return redirect()->to('/private/users');
    }

    public function deactivateUser($id) {
        $userModel = new UserModel();
        $userModel->deactivateUser($id);
        return redirect()->to('/private/users');
    }
}