<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class NavigationController
 */
class NavigationController extends Controller
{
    /**
     * @Template("@App/Navigation/sidebar_component.html.twig")
     *
     * @param $currentItem
     *
     * @return array
     */
    public function sidebarComponentAction($currentItem)
    {
        $hola ='hola';
        return [
            'currentItem' => $currentItem
        ];
    }

}
