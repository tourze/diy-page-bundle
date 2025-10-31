<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\VisitLog;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

/**
 * @extends AbstractCrudController<VisitLog>
 */
#[AdminCrud(routePath: '/diy-page/visit-log', routeName: 'diy_page_visit_log')]
final class VisitLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VisitLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('访问日志')
            ->setEntityLabelInPlural('访问日志')
            ->setPageTitle('index', '访问日志列表')
            ->setPageTitle('detail', '访问日志详情')
            ->setHelp('index', '查看页面装修组件和元素的访问记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
        ;

        yield AssociationField::new('block', '访问组件')
            ->formatValue(function ($value) {
                if (!$value) {
                    return '';
                }

                assert($value instanceof Block);

                return $value->getTitle() . '(' . $value->getCode() . ')';
            })
        ;

        yield AssociationField::new('element', '访问元素')
            ->formatValue(function ($value) {
                if (!$value) {
                    return '';
                }

                assert($value instanceof Element);

                return $value->getTitle();
            })
        ;

        yield AssociationField::new('user', '访问用户')
            ->formatValue(function ($value) {
                if (!$value) {
                    return '未知用户';
                }

                if (is_object($value) && method_exists($value, 'getUsername')) {
                    return $value->getUsername();
                }

                if (is_object($value) && method_exists($value, '__toString')) {
                    return (string) $value;
                }

                return '用户';
            })
        ;

        yield TextField::new('createdFromIp', '访问IP')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '访问时间');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
            ->disable(Action::EDIT)
            ->disable(Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('block', '访问组件'))
            ->add(EntityFilter::new('element', '访问元素'))
            ->add(EntityFilter::new('user', '访问用户'))
        ;
    }
}
