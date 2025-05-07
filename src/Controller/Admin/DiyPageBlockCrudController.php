<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Block;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPageBlockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Block::class;
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
