<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;

class AdminActionSubscriber implements EventSubscriberInterface
{
    public function onBeforeCrudActionEvent(BeforeCrudActionEvent $event): void
    {
        if (!$adminContext = $event->getAdminContext()) {
            return;
        }

        if (!$crudDto = $adminContext->getCrud()) {
            return;
        }

        if ($crudDto->getEntityFqcn() !== User::class) {
            return;
        }

        // dd($event);
        // $actions = $crudDto->getActionsConfig()->getActions();
        
        // dd($crudDto->getCurrentAction());
        if ($crudDto->getCurrentAction() == 'delete') {
            if (in_array('ROLE_CANNOT_DELETE', $adminContext->getEntity()->getInstance()->getRoles())) {
                $crudDto->getActionsConfig()->disableActions([Action::DELETE]);
                $crudDto->getActionsConfig()->disableActions([Action::BATCH_DELETE]);
                $event->setResponse(new RedirectResponse('admin'));
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => 'onBeforeCrudActionEvent',
        ];
    }
}
