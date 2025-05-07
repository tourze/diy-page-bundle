<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Topic;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPageTopicCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Topic::class;
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
