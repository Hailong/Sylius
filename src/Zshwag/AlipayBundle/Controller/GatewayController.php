<?php

namespace Zshwag\AlipayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Zshwag\AlipayBundle\Entity\GatewayNotification;

class GatewayController extends Controller
{
    public function notifyAction(Request $request)
    {
        $noti = new GatewayNotification();
        $noti->setReceivedAt(time());
        $noti->setContent(serialize([
            'query' => $request->query->all(),
            'request' => $request->request->all(),
        ]));

        $em = $this->getDoctrine()->getManager();
        $em->persist($noti);
        $em->flush();

        return new Response('success');
    }

    public function onAuthAction(Request $request)
    {
        return new Response('success');
    }
}
