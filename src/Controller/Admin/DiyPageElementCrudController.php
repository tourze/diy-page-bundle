<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Element;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPageElementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Element::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
