<?php

namespace App\Controller\Admin;

use App\Entity\DataSchema;
use App\Entity\GroupTag;
use App\Entity\OutputSchema;
use App\Service\Admin\ParsingFormService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractDashboardController
{

    public function __construct(
        private readonly ParsingFormService $formService
    ) {}

    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        $form = $this->formService->createStartParsingForm();
        return $this->render('control-panel.html.twig', compact('form'));
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('API Scraper');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Панель управления', 'fa fa-gear');
        yield MenuItem::linkToCrud('Схема запроса', 'fa fa-file-code-o', DataSchema::class);
        yield MenuItem::linkToCrud('Схема данных', 'fa fa-file-arrow-down', OutputSchema::class);
        yield MenuItem::linkToCrud('Группы', 'fa fa-group', GroupTag::class);
    }
}
