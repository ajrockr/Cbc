<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield TextField::new('email');

        yield TextField::new('plainPassword', 'password')
            ->setFormType(PasswordType::class)
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->hideWhenUpdating()
            ->setLabel('Password')
            ->hideOnIndex();

        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER', 'ROLE_REPORT_ADMIN', 'ROLE_PEOPLE_ADMIN', 'ROLE_ASSET_ADMIN', 'ROLE_ASSET_DISPATCHER', 'ROLE_REPAIR_VIEWER', 'ROLE_REPAIR_ADMIN', 'ROLE_CARTS_ADMIN', 'ROLE_ALLOWED_TO_SWITCH', 'ROLE_USERS_ADMIN', 'ROLE_CANNOT_DELETE'];
        yield ChoiceField::new('roles')
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->renderAsBadges();
        yield DateField::new('updatedAt');

        yield DateField::new('createdAt');

        yield BooleanField::new('Enabled')
            ->setPermission('ROLE_USERS_ADMIN');

        yield ImageField::new('avatar')
            ->onlyOnForms()
            ->setBasePath('uploads/avatars')
            ->setUploadDir('public/uploads/avatars')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]');

        yield AvatarField::new('avatar')
            ->onlyOnIndex()
            ->formatValue(static function ($value, ?User $user) {
                return $user?->getAvatarUrl();
            });
        
        yield TextField::new('theme')
            ->onlyOnForms();
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->setPermission(Action::INDEX, 'ROLE_ADMIN')
            ->setPermission(Action::DETAIL, 'ROLE_USERS_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_USERS_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_USERS_ADMIN')
            ->setPermission(Action::BATCH_DELETE, 'ROLE_USERS_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_USERS_ADMIN');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encryptPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // $this->encryptPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function encryptPassword(User $user)
    {
        if ($user->getPlainPassword() != null) {
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
        }
    }

}
