<?php

declare(strict_types=1);

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\ElementAttribute;
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
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<ElementAttribute>
 */
#[AdminCrud(routePath: '/diy-page/element-attribute', routeName: 'diy_page_element_attribute')]
final class ElementAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ElementAttribute::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('元素属性')
            ->setEntityLabelInPlural('元素属性')
            ->setPageTitle('index', '元素属性列表')
            ->setPageTitle('new', '创建元素属性')
            ->setPageTitle('edit', '编辑元素属性')
            ->setPageTitle('detail', '元素属性详情')
            ->setHelp('index', '管理装修元素的自定义属性配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'value', 'remark'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield AssociationField::new('element', '所属元素')
            ->setRequired(true)
            ->formatValue(static function ($value): string {
                if (!$value) {
                    return '';
                }

                assert($value instanceof Element);

                return $value->getTitle() . '(' . $value->getId() . ')';
            })
        ;

        yield TextField::new('name', '属性名')
            ->setRequired(true)
            ->setMaxLength(30)
            ->setHelp('元素属性的标识名称')
        ;

        yield TextField::new('value', '属性值')
            ->setRequired(true)
            ->setMaxLength(255)
            ->setHelp('元素属性的具体值')
        ;

        yield TextField::new('remark', '备注')
            ->setMaxLength(100)
            ->hideOnIndex()
            ->setHelp('对此属性的说明或备注')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('element', '所属元素'))
            ->add(TextFilter::new('name', '属性名'))
            ->add(TextFilter::new('value', '属性值'))
        ;
    }
}
