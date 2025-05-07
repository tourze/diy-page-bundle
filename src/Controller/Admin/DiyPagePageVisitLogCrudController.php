<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\PageVisitLog;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPagePageVisitLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PageVisitLog::class;
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
