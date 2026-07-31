<?php

namespace app\controllers;

use app\core\Controller;

class HomeController extends Controller
{
    /**
     * Landing page pública (marketing) — equivalente ao Home.tsx do Figma.
     * Não exige login; serve tanto para visitantes quanto para quem
     * clicou em "Explorar sem login" na tela de auth.
     */
    public function index()
    {
        $this->view('home/index');
    }
}
