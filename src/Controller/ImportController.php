<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Entity\Asset;
use App\Entity\Import;
use App\Entity\Person;
use App\Repository\SlotRepository;
use App\Repository\AssetRepository;
use App\Repository\PersonRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Repository\CartSlotRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class ImportController extends AbstractController
{
    #[Route('/import', name: 'app_import')]
    public function index(): Response
    {
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
        ]);
    }

    #[Route('/import/people', name: 'app_import_people')]
    public function importPeople(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine, PersonRepository $personRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('csv', FileType::class, [
                 'label' => 'People Import (csv)',
                 'required' => true,
                 'mapped' => true,
                 'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/csv',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV document',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-primary'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data
            $file = $form->get('csv')->getData();
            
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            // // @todo upload file or import into database for future reference???
            // try {
            //     $file->move(
            //         $this->getParameter('imports_directory'),
            //         $newFilename
            //     );
            // } catch (FileException $e) {
            //     throw new FileException($e->getMessage());
            // }

            // Set the entities needed to persist data into the database
            $entityManager = $doctrine->getManager();
            $importEntity = new Import;
            $user = $this->getUser();

            // Prep and import the data
            $rows = array_map('str_getcsv', file($file->getPathname()));
            $header = array_shift($rows);
            $csv = [];
            foreach ($rows as $row) {
                $csv[] = array_combine($header, $row);
            }

            // $progressBar = new ProgressBar($output);
            // $progressBar->setMaxSteps(count($csv));

            $currentExecutionTimeLimit = ini_get('max_execution_time');
            ini_set('max_execution_time', 0);

            try {
                // $progressBar->start();
                foreach ($csv as $row) {
                    $personEntity = new Person;
                    if (null !== $result = $personRepository->findOneBy([
                        'last_name' => $row['last_name'],
                        'first_name' => $row['first_name'],
                        'graduation_year' => $row['graduation_year'],
                        'email' => $row['email']
                    ])) {
                        $result
                            ->setFirstName($row['first_name'])
                            ->setLastName($row['last_name'])
                            // ->setMiddleName($row['middle_name'])
                            ->setGraduationYear($row['graduation_year'])
                            ->setEmail($row['email'])
                        ;
                    } else {
                        $personEntity
                            ->setFirstName($row['first_name'])
                            ->setLastName($row['last_name'])
                            // ->setMiddleName($row['middle_name'])
                            ->setGraduationYear($row['graduation_year'])
                            ->setEmail($row['email'])
                        ;
                        $entityManager->persist($personEntity);
                    }
                    $entityManager->flush();
                    // $progressBar->advance();
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            // Log the import made for future reference
            $importEntity
                ->setImportedAt(new \DateTimeImmutable("now"))
                ->setImportedBy($user)
                ->setImportedData($csv)
            ;
            $entityManager->persist($importEntity);
            $entityManager->flush();

            // $progressBar->finish();
            ini_set('max_execution_time', $currentExecutionTimeLimit);
        }

        return $this->render('import/people.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/import/asset', name: 'app_import_asset')]
    public function importAsset(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine, AssetRepository $assetRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('csv', FileType::class, [
                 'label' => 'Asset Import (csv)',
                 'required' => true,
                 'mapped' => true,
                 'constraints' => [
                    new File([
                        'mimeTypes' => [
                            // 'text/csv',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV document',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-primary'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data
            $file = $form->get('csv')->getData();
            
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            // // @todo upload file or import into database for future reference???
            // try {
            //     $file->move(
            //         $this->getParameter('imports_directory'),
            //         $newFilename
            //     );
            // } catch (FileException $e) {
            //     throw new FileException($e->getMessage());
            // }

            // Set the entities needed to persist data into the database
            $entityManager = $doctrine->getManager();
            $importEntity = new Import;
            $user = $this->getUser();

            // Prep and import the data
            $rows = array_map('str_getcsv', file($file->getPathname()));
            $header = array_shift($rows);
            $csv = [];
            foreach ($rows as $row) {
                $csv[] = array_combine($header, $row);
            }

            $currentExecutionTimeLimit = ini_get('max_execution_time');
            ini_set('max_execution_time', 0);

            try {
                $progressBarMaxLength = count($csv);
                $progressBarCurrentStep = 0;

                foreach ($csv as $row) {
                    $progressBarCurrentStep += 1;
                    $progressBarPercent = intval($progressBarCurrentStep/$progressBarMaxLength * 100) . '%';
                    if ($request->request->get('progressBarCurrentAjax')) {
                        $arrData = ['progressBarCurrentValue' => $progressBarPercent];
                        return new JsonResponse($arrData);
                    }

                    $assetEntity = new Asset;
                    if (null !== $result = $assetRepository->findOneBy([
                        'asset_tag' => $row['asset_tag'],
                    ])) {
                        $result
                            ->setAssetTag($row['asset_tag'])
                            ->setNeedsRepair(false)
                        ;
                    } else {
                        $assetEntity
                            ->setAssetTag($row['asset_tag'])
                            ->setNeedsRepair(false)
                        ;
                        $entityManager->persist($assetEntity);
                    }
                    $entityManager->flush();
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            // Log the import made for future reference
            $importEntity
                ->setImportedAt(new \DateTimeImmutable("now"))
                ->setImportedBy($user)
                ->setImportedData($csv)
            ;
            $entityManager->persist($importEntity);
            $entityManager->flush();

            ini_set('max_execution_time', $currentExecutionTimeLimit);
        }

        return $this->render('import/asset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/import/data', name: 'app_import_data')]
    public function importSlotRelationalData(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrine, AssetRepository $assetRepository, PersonRepository $personRepository, SlotRepository $slotRepository, CartSlotRepository $cartslotRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('csv', FileType::class, [
                 'label' => 'Asset Import (csv)',
                 'required' => true,
                 'mapped' => true,
                 'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/csv',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV document',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-primary'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data
            $file = $form->get('csv')->getData();
            
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            // // @todo upload file or import into database for future reference???
            // try {
            //     $file->move(
            //         $this->getParameter('imports_directory'),
            //         $newFilename
            //     );
            // } catch (FileException $e) {
            //     throw new FileException($e->getMessage());
            // }

            // Set the entities needed to persist data into the database
            $entityManager = $doctrine->getManager();
            $importEntity = new Import;
            $user = $this->getUser();

            // Prep and import the data
            $rows = array_map('str_getcsv', file($file->getPathname()));
            $header = array_shift($rows);
            $csv = [];
            foreach ($rows as $row) {
                $csv[] = array_combine($header, $row);
            }

            // dd($csv);

            $currentExecutionTimeLimit = ini_get('max_execution_time');
            ini_set('max_execution_time', 0);

            try {
                foreach ($csv as $row) {
                    // Get the Asset entity
                    $assetEntity = $entityManager->getRepository('App\Entity\Asset')
                        ->findOneBy(['asset_tag' => $row['asset_tag']]);

                    // Get the Slot entity
                    $slotEntity = new Slot;

                    // Get the CartSlot entity based on the csv row
                    $cartslotEntity = $cartslotRepository->findOneBy([ 'cart_slot_number' => $row['slot_number'] ]);

                    // Get the Person entity based on last_name, first_name
                    $name = explode(', ', $row['assigned_person']);
                    $personEntity = $personRepository->findOneBy([ 'first_name' => $name[1], 'last_name' => $name[0] ]);

                    // Set the desired information
                    $finished = ($row['finished'] == 1) ? true : false;
                    $repair = ($row['repair'] == 1) ? true : false;

                    try {
                        $slotEntity->setNumber($cartslotEntity);
                        $slotEntity->setAssignedAssetId($assetEntity);
                        $slotEntity->setAssignedPersonId($personEntity);
                        $slotEntity->setIsFinished($finished);
                        $assetEntity->setNeedsRepair($repair);
                        $assetEntity->setNotes($row['notes']);
                        $entityManager->persist($slotEntity);
                        $entityManager->flush();
                    } catch(UniqueConstraintViolationException $e) {
                        $doctrine->resetManager();
                        dump($e->getMessage());
                        dump($e->getQuery());
                    }
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            // Log the import made for future reference
            // $importEntity
            //     ->setImportedAt(new \DateTimeImmutable("now"))
            //     ->setImportedBy($user)
            //     ->setImportedData($csv)
            // ;
            // $entityManager->persist($importEntity);
            // $entityManager->flush();

            ini_set('max_execution_time', $currentExecutionTimeLimit);
        }

        return $this->render('import/data.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
