<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\PageTag;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPagePageTagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageTag::class;
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
