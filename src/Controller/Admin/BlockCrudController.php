<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Block;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * @extends AbstractCrudController<Block>
 */
#[AdminCrud(routePath: '/diy-page/block', routeName: 'diy_page_block')]
final class BlockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Block::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('广告位')
            ->setEntityLabelInPlural('广告位')
            ->setPageTitle('index', '广告位列表')
            ->setPageTitle('new', '创建广告位')
            ->setPageTitle('edit', '编辑广告位')
            ->setPageTitle('detail', '广告位详情')
            ->setHelp('index', '管理页面装修组件广告位配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'code', 'typeId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield TextField::new('title', '标题')
            ->setRequired(true)
            ->setMaxLength(100)
        ;

        yield TextField::new('code', '唯一标志')
            ->setRequired(true)
            ->setMaxLength(100)
            ->setHelp('用于程序中识别广告位的唯一编码')
        ;

        yield TextField::new('typeId', '类型ID')
            ->setMaxLength(40)
            ->hideOnIndex()
        ;

        yield IntegerField::new('sortNumber', '排序')
            ->setHelp('数值越小排序越靠前')
        ;

        yield BooleanField::new('valid', '有效状态');

        yield AssociationField::new('elements', '包含元素')
            ->hideOnForm()
            ->formatValue(static function ($value): string {
                if (!$value) {
                    return '无';
                }

                assert($value instanceof Collection);
                $count = $value->count();

                return $count > 0 ? "{$count} 个元素" : '无元素';
            })
        ;

        yield DateTimeField::new('beginTime', '开始时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('endTime', '结束时间')
            ->hideOnIndex()
        ;

        yield TextareaField::new('showExpression', '显示规则')
            ->hideOnIndex()
            ->setHelp('控制广告位显示的表达式规则')
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
            ->add(TextFilter::new('title', '标题'))
            ->add(TextFilter::new('code', '唯一标志'))
            ->add(TextFilter::new('typeId', '类型ID'))
            ->add(BooleanFilter::new('valid', '有效状态'))
        ;
    }
}
