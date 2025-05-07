<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Point;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPagePointCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Point::class;
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
