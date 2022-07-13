<?php

namespace App\Controller;

use Exception;
use App\Entity\Cart;
use App\Entity\Slot;
use App\Entity\Asset;
use App\Entity\Repair;
use App\Entity\CartSlot;
use App\Entity\RepairItem;
use App\Repository\CartRepository;
use App\Repository\SlotRepository;
use App\Repository\AssetRepository;
use App\Repository\PersonRepository;
use App\Repository\CartSlotRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class CartController extends AbstractController
{
    // @todo Might make $attributes into something else later
    private function createSlotEditForm(mixed $attributes, ObjectManager $doctrine): FormInterface
    {
        return $this->createFormBuilder()
            ->add('inputPersonName', ChoiceType::class, [
                'label' => 'Person',
                'choices' => $attributes,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'inputPersonName',
                    'data-placeholder' => 'Select a person...'
                ]
            ])
            ->add('inputAssetTag', TextType::class, [
                'label' => ' ',
                'attr' => [
                    'placeholder' => 'Asset Tag',
                    'id' => 'inputAssetTag',
                    'step' => 1,
                    'class' => 'form-control',
                    'autofocus' => true
                ]
            ])
            ->add('inputNumberGenerator', ButtonType::class, [
                'label' => 'Generate',
                'attr' => [
                    'class' => 'btn btn-outline-secondary',
                ]
            ])
            ->add('inputAssetNotes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'inputAssetNotes'
                ]
            ])
            ->add('needsRepair', CheckboxType::class, [
                'label' => 'Needs Repair',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('inputRepairItems', EntityType::class, [
                'class' => RepairItem::class,
                'choice_label' => function (RepairItem $item) {
                    return $item->getName();
                },
                'choice_attr' => function (RepairItem $ri, $key, $value) {
                    return [
                        'class' => 'form-check-input m-1',
                        // 'data-slot-repairitem' => $value
                    ];
                },
                // 'mapped' => false,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('slotFinished', CheckboxType::class, [
                'label' => 'Finished',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('clear', SubmitType::class, [
                'label' => 'Clear',
                'attr' => [
                    'class' => 'btn btn-danger'
                ]
            ])
            ->add('repair', SubmitType::class, [
                'label' => 'Repair',
                'attr' => [
                    'class' => 'btn btn-secondary disabled'
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
            ->add('slot_id', HiddenType::class)
            ->getForm()
        ;
    }

    #[Route('/cart/show/all', name: 'app_cart_show_all')]
    public function showAll(Security $security, Request $request, CartRepository $cartRepository, CartSlotRepository $CartSlotRepository, SlotRepository $SlotRepository, PersonRepository $PersonRepository, AssetRepository $AssetRepository, ManagerRegistry $doctrine): Response
    {
        // Set an empty variable of inputErrors for template rendering
        $insertErrors = '';

        // Get the Doctrine Entity Manager
        $em = $doctrine->getManager();

        // Generate a list of items from the People table
        $people = $PersonRepository->findAll();
        foreach ($people as $p) {
            $personList[($p->getLastName() . ', ' . $p->getFirstName() . ' (' . $p->getGraduationYear() . ')')] = $p->getId();
        }

        // Loop through the carts to get the slots for that cart
        foreach ($cartRepository->getCartNumbers() as $cart) {
            $data[$cart['cart_number']] = $CartSlotRepository->getCartSlots($cart['cart_number']);
        }

        // Get the assigned slots
        $slotData = $SlotRepository->findSlots();

        // Create the form that will be used to edit a slot
        $form = $this->createSlotEditForm($personList, $em);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data that was passed
            $input = $form->getData();

            // If the Clear button was clicked
            if ($form->getClickedButton() && 'clear' === $form->getClickedButton()->getName()) {
                $SlotRepository->clearSlot($input['slot_id']);
                return $this->redirect($request->headers->get('referer'));
            }

            // If the repair button was clicked
            if ($form->getClickedButton() && 'repair' === $form->getClickedButton()->getName()) {
                $this->redirectToRoute('app_repair_add', ['test' => 'test data']);
            }

            // Get the Person entity
            $personEntity = $em
                ->getRepository('App\Entity\Person')
                ->findOneBy([
                    'id' => $input['inputPersonName']
                ])
            ;

            // Get the CartSlot entity
            $cartslotEntity = $em
                    ->getRepository('App\Entity\CartSlot')
                    ->findOneBy([
                        'id' => $input['slot_id']
                    ])
            ;

            // Does the asset submitted exist?
            $asset = $AssetRepository->findOneBy([
                'asset_tag' => $input['inputAssetTag']
            ]);

            // Create the asset if it doesn't exist
            if (null == $asset) {
                // Get the Asset entity, set the properties and persist/flush into database
                $ae = new Asset();
                $ae->setAssetTag($input['inputAssetTag']);
                $ae->setNeedsRepair($input['needsRepair']);
                $em->persist($ae);
                $em->flush();

                // We have to do this again since the record didn't exist, otherwise the slot save action will input NULL into database
                $asset = $AssetRepository->findOneBy([
                    'asset_tag' => $input['inputAssetTag']
                ]);
            } else {
                $assetEntity = $em->getRepository(Asset::class)->findOneBy(['asset_tag' => $input['inputAssetTag']]);
                $assetEntity->setNeedsRepair($input['needsRepair']);
                $em->flush();
            }

            // dd($input);
            // Assign the asset with the assetId and personId
            try {
                // Get the Slot entity, set the required items and save into database
                $slotEntity = $em->getRepository(Slot::class)->findOneBy(['number' => $input['slot_id']]);

                if ($slotEntity) {
                    $slotEntity->setAssignedAssetId($asset);
                    $slotEntity->setAssignedPersonId($personEntity);
                    $slotEntity->setNumber($cartslotEntity);
                    $slotEntity->setIsFinished($input['slotFinished']);
                    $em->flush();
                } else {
                    $se = new Slot();
                    $se->setAssignedPersonId($personEntity);
                    $se->setAssignedAssetId($asset);
                    $se->setNumber($cartslotEntity);
                    $se->setIsFinished($input['slotFinished']);
                    $em->persist($se);
                    $em->flush();
                }

                // Get repair information
                if (isset($input['inputRepairItems']) && !empty($input['inputRepairItems'])) {
                    $repairEntity = new Repair();
                    
                    foreach ($input['inputRepairItems'] as $repairItem) {
                        $repairItems[] = $repairItem->getId();
                    }

                    // @todo check if a repair already exists for asset
                    $repairEntity
                        ->setAssetid($input['inputAssetTag'])
                        ->setPersonid($input['inputPersonName'])
                        ->setTechnicianid($security->getUser()->getId())
                        ->setCreatedAt(new \DateTimeImmutable("now"))
                        ->setModifiedAt(new \DateTimeImmutable("now"))
                        ->setCartSlotId($input['slot_id'])
                        ->setActive(true)
                        ->setNotes($input['inputAssetNotes'])
                        ->setItems($repairItems);

                    $em->persist($repairEntity);
                    $em->flush();
                }
            } catch (Exception $e) {
                // We have an error, assuming record already exists
                // @todo Will probably have to do more testing for other possible errors and handle them accordingly
                $insertErrors .= "Something Went Wrong.\r\n";
                throw new Exception($e->getMessage());
        
                // We render the template here, had a hard time redirecting with passing the inputErrors variable
                return $this->render('cart/show.cart.html.twig', [
                    'cartData' => $slotData,
                    'editForm' => $form->createView(),
                    'insert_errors' => $insertErrors
                ]);
            }
            
            return $this->redirectToRoute('app_cart_show_all');
        }

        // Form has not been submitted yet, render the template
        return $this->render('cart/show.cart.html.twig', [
            'cartData' => $slotData,
            'editForm' => $form->createView(),
            'insert_errors' => $insertErrors
        ]);
    }

    #[Route('/cart/show/{CartNumber<\d+>}', name: 'app_cart_show_number')]
    public function showOne(Request $request, CartSlotRepository $CartSlotRepository, SlotRepository $SlotRepository, PersonRepository $PersonRepository, AssetRepository $AssetRepository, ManagerRegistry $doctrine, int $CartNumber): Response
    {
        // Set an empty variable of inputErrors for template rendering
        $insertErrors = '';

        // Generate a list of items from the People table
        $people = $PersonRepository->findAll();
        foreach ($people as $p) {
            $personList[($p->getLastName() . ', ' . $p->getFirstName() . ' (' . $p->getGraduationYear() . ')')] = $p->getId();
        }

        // Get the assigned slots
        $slotData = $SlotRepository->findSlots($CartNumber);

        // Create the form that will be used to edit a slot
        $form = $this->createSlotEditForm($personList);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data that was passed
            $input = $form->getData();

            // If the Clear button was clicked
            if ($form->getClickedButton() && 'clear' === $form->getClickedButton()->getName()) {
                $SlotRepository->clearSlot($input['slot_id']);
                return $this->redirect($request->headers->get('referer'));
            }

            // If the repair button was clicked
            if ($form->getClickedButton() && 'repair' === $form->getClickedButton()->getName()) {

            }

            // Get the Doctrine Entity Manager
            $em = $doctrine->getManager();

            // Get the Person entity
            $personEntity = $em
                ->getRepository('App\Entity\Person')
                ->findOneBy([
                    'id' => $input['inputPersonName']
                ])
            ;

            // Get the CartSlot entity
            $cartslotEntity = $em
                    ->getRepository('App\Entity\CartSlot')
                    ->findOneBy([
                        'id' => $input['slot_id']
                    ])
            ;

            // Does the asset submitted exist?
            $asset = $AssetRepository->findOneBy([
                'asset_tag' => $input['inputAssetTag']
            ]);

            // Create the asset if it doesn't exist
            if (null == $asset) {
                // Get the Asset entity, set the properties and persist/flush into database
                $ae = new Asset();
                $ae->setAssetTag($input['inputAssetTag']);
                $ae->setNeedsRepair($input['needsRepair']);
                $em->persist($ae);
                $em->flush();

                // We have to do this again since the record didn't exist, otherwise the slot save action will input NULL into database
                $asset = $AssetRepository->findOneBy([
                    'asset_tag' => $input['inputAssetTag']
                ]);
            } else {
                $assetEntity = $em->getRepository(Asset::class)->findOneBy(['asset_tag' => $input['inputAssetTag']]);
                $assetEntity->setNeedsRepair($input['needsRepair']);
                $em->flush();
            }

            // Assign the asset with the assetId and personId
            try {
                // Get the Slot entity, set the required items and save into database
                $slotEntity = $em->getRepository(Slot::class)->findOneBy(['number' => $input['slot_id']]);
                if ($slotEntity) {
                    $slotEntity->setAssignedAssetId($asset);
                    $slotEntity->setAssignedPersonId($personEntity);
                    $slotEntity->setNumber($cartslotEntity);
                    $slotEntity->setIsFinished($input['slotFinished']);
                    $em->flush();
                } else {
                    $se = new Slot();
                    $se->setAssignedPersonId($personEntity);
                    $se->setAssignedAssetId($asset);
                    $se->setNumber($cartslotEntity);
                    $se->setIsFinished($input['slotFinished']);
                    $em->persist($se);
                    $em->flush();
                }
            } catch (Exception $e) {
                // We have an error, assuming record already exists
                // @todo Will probably have to do more testing for other possible errors and handle them accordingly
                $insertErrors = 'Asset or person already assigned to a slot.';
        
                // We render the template here, had a hard time redirecting with passing the inputErrors variable
                return $this->render('cart/show.cart.html.twig', [
                    'cart_number' => $CartNumber,
                    'cartData' => $slotData,
                    'editForm' => $form->createView(),
                    'insert_errors' => $insertErrors
                ]);
            }
            
            return $this->redirectToRoute('app_cart_show_number', [
                'CartNumber' => $CartNumber
            ]);
        }

        // dd($as);
        // Form has not been submitted yet, render the template
        return $this->render('cart/show.cart.html.twig', [
            'cart_number' => $CartNumber,
            'cartData' => $slotData,
            'editForm' => $form->createView(),
            'insert_errors' => $insertErrors
        ]);
    }

    #[Route('/cart/add/{number<\d+>}', name: 'app_cart_add')]
    public function add(Request $request, CartRepository $CartRepository, CartSlotRepository $CartSlotRepository, ManagerRegistry $doctrine, int|null $number = null): Response
    {
        // Set an empty insertErrors variable for template
        $insertErrors = '';

        // Get carts for list rendering
        $carts = $CartRepository->findAll();
        foreach($carts as $cart) {
            $data[] = $cart->getCartNumber();
        }

        // Generate the cart add form
        $cartForm = $this->createFormBuilder()
            ->add('number', NumberType::class, [
                'label' => 'Cart Number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add Cart',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->add('close', ButtonType::class, [
                'label' => 'Close',
                'attr' => [
                    'class' => 'btn btn-secondary',
                    'data-bs-dismiss' => 'modal'
                ]
            ])
            ->getForm()
        ;

        if (null != $number) {
            // Generate the form and user input
            $slotForm = $this->createFormBuilder()
                ->add('row', ChoiceType::class, [
                    // TODO Make 1 be default
                    'choices' => range(0,10),
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'data' => 1
                ])
                ->add('side', ChoiceType::class, [
                    'choices' => range(0,1),
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ])
                ->add('slot', TextareaType::class, [
                    'attr' => ['class' => 'tinymce'],
                    'help' => 'Seperate multiple slots with a , or a range #-#.',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new Regex('/^\d+(?:,\d+)|(?:-\d+)*$/')
                    ],
                ])
                ->add('save', SubmitType::class, [
                    'label' => 'Add Slot',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
                ])
                ->add('close', ButtonType::class, [
                    'label' => 'Close',
                    'attr' => [
                        'class' => 'btn btn-secondary',
                        'data-bs-dismiss' => 'modal',
                    ]
                ])
                ->getForm()
            ;

            // Handle the slot add form
            $slotForm->handleRequest($request);
            if ($slotForm->isSubmitted() && $slotForm->isValid()) {
                // Get form data
                $input = $slotForm->getData();
                $errors = $slotForm->getErrors();

                if (str_contains($input['slot'], '-')) {
                    $s = array_map('trim', explode('-', $input['slot']));
                    $slots = range($s[0], $s[1]);
                } else {
                    $slots = array_map('trim', explode(',', $input['slot']));
                }

                // Insert db record
                foreach ($slots as $slot) {
                    if (false == $CartSlotRepository->addSlot((new CartSlot), cart: $number, row: $input['row'], slot: $slot, side: $input['side'])) {
                        $insertErrors .= 'Error adding slot in cart '.$number.', Slot: '.$slot.', Row: '.$input['row'].', Side: '.$input['side'] . '\r\n';
                    }
                }
            }
        }

        // Handle the cart add form
        $cartForm->handleRequest($request);
        if ($cartForm->isSubmitted() && $cartForm->isValid()) {
            // Get form data
            $input = $cartForm->getData();
            $insertErrors = $cartForm->getErrors() . '\r\n';

            // Get doctrine entity manager
            $entityManger = $doctrine->getManager();

            // Get cart entity, set entity properties and save to database
            try {
                $cartEntity = new Cart();
                $cartEntity->setCartNumber($input['number']);
                $cartEntity->setCartDescription($input['description']);
                $entityManger->persist($cartEntity);
                $entityManger->flush();
            } catch (Exception $e) {
                $insertErrors .= 'Cart could not be added into database.\r\n';
            } finally {
                return $this->redirectToRoute('app_cart_add', [
                    'number' => $input['number']
                ]);
            }
        }

        if (null != $number) {
            return $this->render('cart/add.html.twig', [
                'slotForm' => $slotForm->createView(),
                'cart_number' => $number,
                'insert_errors' => $insertErrors,
                'cart_links' => $data,
                'cart_render' => $CartSlotRepository->getCartSlots($number),
                'cartForm' => $cartForm->createView()
            ]);
        }

        return $this->render('cart/add.html.twig', [
                'cart_links' => $data,
                'cartForm' => $cartForm->createView()
        ]);
    }

    #[Route('/cart/add/check/person', name: 'app_cart_add_check_person')]
    public function checkPerson(int $personId, ManagerRegistry $doctrine): bool
    {
        $em = $doctrine->getManager();

        if (null !== $em
                        ->getRepository('App\Entity\Slot')
                        ->findBy([
                            'assignedPersonId' => $personId
                        ])) {
            return true;
        }

        return false;
    }

    private function clearSlot(SlotRepository $slotRepository, int $slotId): void
    {
        // $em = $doctrine->getManager();
        // if (null !== $slot = $em
        //         ->getRepository('App\Entity\Slot')
        //         ->findOneBy([
        //             'number' => $slotId
        //         ])) {
        //     $em->remove($slot);
        //     $em->flush();
        // }
        // $slotRepository->clearSlot($slotId);
    }

    public function generateCartList(CartSlotRepository $CartSlotRepository, Session $session): Response
    {
        $results = $CartSlotRepository->findAll();
        foreach ($results as $result) {
            $c[] = $result->getCartNumber();
        }

        // Make sure we are only getting 1 entity representing each cart
        $carts = array_unique($c);

        $session->set('navitem_carts', $carts);

        dd($carts);

        return $this->render('cart/navmenu.html.twig', [
            'carts' => $carts,
        ]);
    }
}
