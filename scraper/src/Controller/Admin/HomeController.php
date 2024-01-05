<?php

namespace App\Controller\Admin;

use App\Entity\DataSchema;
use App\Entity\GroupTag;
use App\Entity\OutputSchema;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractDashboardController
{

    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator) {}

    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(OutputSchemaCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Схемы запросов');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Схема запроса', 'fa fa-file-code-o', DataSchema::class);
        yield MenuItem::linkToCrud('Схема данных', 'fa fa-file-arrow-down', OutputSchema::class);
        yield MenuItem::linkToCrud('Группы', 'fa fa-group', GroupTag::class);
    }
}
