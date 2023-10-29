<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class MainController extends BaseController
{
    public function index()
    {
        // If the user is logged in, redirect to the private area
        if (logged_in()) return redirect()->to('/private');
        
        return view('main');
    }

    // POST index
    public function shorten()
    {
        $urlModel = new \App\Models\UrlModel();
        $user_id = user_id();
        $route = $this->request->getPost('route');
        $url = $this->request->getPost('url');
        $shortname = $this->request->getPost('shortname');
        $description = $this->request->getPost('description');
        
        $created_at = date('Y-m-d H:i:s');
        $expiration = $this->request->getPost('expiration');
        if ($expiration == '') $expiration = null;
        // If the user is not logged in, the expiration date is 6 months
        if (!logged_in()) $expiration = date('Y-m-d H:i:s', strtotime($expiration . ' + 6 months'));
        if ($urlModel->urlExists($route)) 
            return redirect()->back()->withInput()->with('error', 'Aquesta ruta ja està en ús');
        
        if ($route == null) {
            // Generate a random route for the URL
            $route = bin2hex(random_bytes(3));
            // Check if the route already exists
            while ($urlModel->urlExists($route)) {
                $route = bin2hex(random_bytes(3));
            }
        }

        // Insert the URL into the database
        $urlModel->newUrl([
            'route' => $route,
            'url' => $url,
            'shortname' => $shortname,
            'description' => $description,
            'user_id' => $user_id,
            'created_at' => $created_at,
            'expires' => $expiration,
        ]);
        
        return redirect()->to('/your-url')->with('route', site_url($route));
    }

    // GET /your-url
    public function yourUrl()
    {
        if (!session('route')) return redirect()->to(site_url());

        $data['route'] = session('route');
        return view('your_url', $data);
    }

    // GET /{route}
    public function preUrl($route)
    {
        // If description is null, redirecto to POST /{route}
        $urlModel = new \App\Models\UrlModel();
        if (!$urlModel->urlExists($route)) return view('errors/html/error_404', ['heading' => '404', 'message' => 'Comprova que la URL sigui correcta']);
        if (!$urlModel->isUrlActive($route)) return view('errors/html/error_404', ['heading' => '404', 'message' => 'Aquesta URL ha estat desactivada per un administrador']);
        if ($urlModel->isUrlExpired($route)) return view('errors/html/error_404', ['heading' => '404', 'message' => 'Aquesta URL ha expirat']);
        $url = $urlModel->getUrlByRoute($route);
        if ($url['description'] == null) return $this->redirect($route);
        return view('pre_url', 
        [
            'url' => $url, 
            'description' => $url['description'], 
            'title' => $url['shortname'],
        ]);
    }

    // POST /{route}
    public function redirect($route)
    {
        $urlModel = new \App\Models\UrlModel();
        $visitModel = new \App\Models\VisitModel();
        $url = $urlModel->getUrlByRoute($route);
        $visitModel->newVisit($url['route'], $this->request->getIPAddress());
        return redirect()->to($url['url']);
    }
}
