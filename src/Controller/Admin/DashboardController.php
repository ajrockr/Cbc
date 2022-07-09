<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Config;
use App\Entity\Import;
use App\Entity\Repair;
use App\Entity\RepairItem;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private ChartBuilderInterface $chartBuilder;

    public function __construct(ChartBuilderInterface $chartBuilder)
    {
        $this->chartBuilder = $chartBuilder;
    }

    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/index.html.twig', [
            'chart' => $this->createChart(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Chromebook Carts');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section();
            yield MenuItem::linkToDashboard('Admin Dashboard', 'fa fa-dashboard');
            yield MenuItem::linkToRoute('Home', 'fa fa-home', 'app_home');
        yield MenuItem::section('System');
            yield MenuItem::linktoCrud('Config', 'fas fa-cog', Config::class)
                ->setPermission('ROLE_SUPER_ADMIN');
            yield MenuItem::linktoCrud('Users', 'fas fa-person', User::class)
                ->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::section('Carts');
            yield MenuItem::linkToRoute('Add Carts/Slots', 'fas fa-chart-bar', 'app_cart_add')
                ->setPermission('ROLE_CARTS_ADMIN');
            yield MenuItem::linkToRoute('Manage Carts', 'fas fa-box', 'app_manage_cart')
                ->setPermission('ROLE_CARTS_ADMIN');
        yield MenuItem::section('Repairs');
            yield MenuItem::linktoCrud('Repair Items', 'fas fa-toolbox', RepairItem::class);
            yield MenuItem::linktoCrud('Repairs', 'fas fa-wrench', Repair::class);
        yield MenuItem::section('Imports');
            yield MenuItem::linkToCrud('Import History', 'fas fa-timeline', Import::class);
            yield MenuItem::linkToRoute('Import Data', 'fas fa-upload', 'app_import_data');

        // @todo Make Manage Carts make:controller, change template https://stackoverflow.com/questions/38829397/how-to-setup-a-custom-form-page-within-easyadminbundle
    }

    private function createChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ]
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                   'suggestedMin' => 0,
                   'suggestedMax' => 100,
                ],
            ],
        ]);

        return $chart;
    }
}
