<?php

namespace Zshwag\Bundle\WechatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ZshwagWechatBundle:Default:index.html.twig');
    }
}
