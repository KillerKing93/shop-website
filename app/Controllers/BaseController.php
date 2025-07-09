<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\SettingModel;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form', 'text'];
    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Pastikan direktori database ada
        $dbPath = WRITEPATH . 'database';
        if (!is_dir($dbPath)) {
            mkdir($dbPath, 0775, true);
        }

        $this->session = \Config\Services::session();

        // Pastikan folder upload logo & hero selalu ada
        $logoDir = ROOTPATH . 'public/uploads/logos';
        if (!is_dir($logoDir)) {
            mkdir($logoDir, 0777, true);
        }
        $heroDir = ROOTPATH . 'public/uploads/heros';
        if (!is_dir($heroDir)) {
            mkdir($heroDir, 0777, true);
        }
    }
}
