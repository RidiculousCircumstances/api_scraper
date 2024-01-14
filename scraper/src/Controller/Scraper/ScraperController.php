<?php

namespace App\Controller\Scraper;

use App\Message\Scraper\StartScraperCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ScraperController extends AbstractController
{

    #[Route('/parse', 'admin_start_parsing')]
    public function parse(#[MapRequestPayload] StartScraperCommand $data, MessageBusInterface $commandBus): RedirectResponse
    {
        $commandBus->dispatch($data);
        return $this->redirectToRoute('home');
    }

}