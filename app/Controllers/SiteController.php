<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class SiteController extends BaseController
{
    public function index()
    {
        return View('site/index');
    }
}
