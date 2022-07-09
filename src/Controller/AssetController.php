<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Repository\AssetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AssetController extends AbstractController
{
    #[Route('/asset', name: 'app_asset')]
    public function index(Request $request, AssetRepository $assets): Response
    {
        // Get the people
        $asset = $assets->findAll();
        $data = [];
        foreach ($asset as $a) {
            $data[] = [
                'id'                => $a->getId(),
                'asset_tag'        => $a->getAssetTag(),
                'serial_number'         => $a->getSerialNumber(),
            ];
        }

        $form = $this->createFormBuilder()
            ->add('inputAssetTag', TextType::class, [
                'label' => 'Asset Tag',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('inputSerialNumber', TextType::class, [
                'label' => 'Serial Number',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('close', ButtonType::class, [
                'label' => 'Close',
                'attr' => [
                    'class' => 'btn btn-secondary',
                    'data-bs-dismiss' => 'modal',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm()
        ;
        
        // Handle form submission
        $form->handleRequest($request);

        $insertErrors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $input = $form->getData();

            if (
                ! $assets->addAsset((new Asset), [
                    'asset_tag' => $input['inputAssetTag'],
                    'serial_number' => $input['inputSerialNumber'],
                ])
            ) {
                $insertErrors = [
                    'failed adding person into database.'
                ];
            }

            return $this->redirectToRoute('app_asset');
        }

        return $this->render('asset/index.html.twig', [
            'assets' => $data,
            'form' => $form->createView(),
            'errors' => $insertErrors
        ]);
    }
}
