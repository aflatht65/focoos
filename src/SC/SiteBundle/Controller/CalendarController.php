<?php

namespace SC\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SC\UserBundle\Form\Type\ProfilePhotoType;
use SC\UserBundle\Entity\User;

class CalendarController extends Controller {
    /**
     * @Route("/calendar", name="calendar")
     * @Secure(roles="ROLE_USER")
     */
    public function calendarAction() {
        $now = new \DateTime('now');
        $startDate = \DateTime::createFromFormat('d-m-Y', $this->getRequest()->get('startDate', $now->format('d-m-Y')));
        $nbDay = $startDate->format('N');
        $startDate->modify('-'.($nbDay-1).' days 00:00');
        $endDate = clone $startDate;
        $endDate->modify('+6 days 23:59:59');
        $prevDate = new \DateTime('-7 days');
        $nextDate = new \DateTime('+7 days');
        $deadlines = $this->container->get('doctrine')->getRepository('SC\SiteBundle\Entity\Deadline')->findPosts(array(
            'deadlineAfter' => $startDate,
            'deadlineBefore' => $endDate,
        ));
        return $this->render('SCSiteBundle::calendar.html.twig', array(
            'startDate' => $startDate,
            'endDate' => $endDate,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'deadlines' => $deadlines,
        ));
    }
}
