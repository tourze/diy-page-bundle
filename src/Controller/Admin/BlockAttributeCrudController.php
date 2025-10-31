<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\BlockAttribute;
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
 * @extends AbstractCrudController<BlockAttribute>
 */
#[AdminCrud(routePath: '/diy-page/block-attribute', routeName: 'diy_page_block_attribute')]
final class BlockAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlockAttribute::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('组件属性')
            ->setEntityLabelInPlural('组件属性')
            ->setPageTitle('index', '组件属性列表')
            ->setPageTitle('new', '创建组件属性')
            ->setPageTitle('edit', '编辑组件属性')
            ->setPageTitle('detail', '组件属性详情')
            ->setHelp('index', '管理装修组件的自定义属性配置')
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

        yield AssociationField::new('block', '所属组件')
            ->setRequired(true)
            ->formatValue(static function ($value): string {
                if (!$value) {
                    return '';
                }

                assert($value instanceof Block);

                return $value->getTitle() . '(' . $value->getCode() . ')';
            })
        ;

        yield TextField::new('name', '属性名')
            ->setRequired(true)
            ->setMaxLength(30)
            ->setHelp('组件属性的标识名称')
        ;

        yield TextField::new('value', '属性值')
            ->setRequired(true)
            ->setMaxLength(64)
            ->setHelp('组件属性的具体值')
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
        return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('block', '所属组件'))
            ->add(TextFilter::new('name', '属性名'))
            ->add(TextFilter::new('value', '属性值'))
        ;
    }
}
