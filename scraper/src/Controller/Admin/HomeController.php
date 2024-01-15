<?php

namespace App\Controller\Admin;

use App\Entity\DataSchema;
use App\Entity\GroupTag;
use App\Entity\OutputSchema;
use App\Entity\Settings\Settings;
use App\Service\Admin\ControlPanelFormService;
use App\Service\ScraperUI\ScraperStatusStore;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractDashboardController
{

    public function __construct(
        private readonly ControlPanelFormService $formService,
        private readonly ScraperStatusStore      $cacheStore
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/home', name: 'home')]
    public function index(): Response
    {
        $form = $this->formService->createStartParsingForm();

        $scraperMessageCache = $this->cacheStore->getScraperUIMessage();

        $viewParams = [];
        $viewParams['form'] = $form;
        $viewParams['message'] = $scraperMessageCache->get();

        return $this->render('control-panel.html.twig', $viewParams);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('API Scraper');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Панель управления', 'fa fa-microchip');
        yield MenuItem::linkToCrud('Схема запроса', 'fa fa-file-code-o', DataSchema::class);
        yield MenuItem::linkToCrud('Схема парсинга', 'fa fa-file-arrow-down', OutputSchema::class);
        yield MenuItem::linkToCrud('Группы', 'fa fa-group', GroupTag::class);
        yield MenuItem::linkToCrud('Настройки', 'fa fa-gear', Settings::class);
    }
}
