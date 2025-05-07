<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Page;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPagePageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
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
