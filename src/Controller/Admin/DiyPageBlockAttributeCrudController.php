<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\BlockAttribute;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPageBlockAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlockAttribute::class;
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
