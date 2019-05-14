<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {

        return $this->render(
            'index.html.twig'
        );
    }
}