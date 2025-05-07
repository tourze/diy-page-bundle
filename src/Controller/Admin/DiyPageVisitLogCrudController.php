<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\VisitLog;
use Tourze\EasyAdminExtraBundle\Controller\AbstractCrudController;

class DiyPageVisitLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VisitLog::class;
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
