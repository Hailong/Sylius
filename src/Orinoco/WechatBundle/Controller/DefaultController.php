<?php

namespace Orinoco\WechatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OrinocoWechatBundle:Default:index.html.twig');
    }
}
