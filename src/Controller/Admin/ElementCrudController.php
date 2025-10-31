<?php

namespace DiyPageBundle\Controller\Admin;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Tourze\FileStorageBundle\Field\ImageGalleryField;

/**
 * @extends AbstractCrudController<Element>
 */
#[AdminCrud(routePath: '/diy-page/element', routeName: 'diy_page_element')]
final class ElementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Element::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('图片元素')
            ->setEntityLabelInPlural('图片元素')
            ->setPageTitle('index', '图片元素列表')
            ->setPageTitle('new', '创建图片元素')
            ->setPageTitle('edit', '编辑图片元素')
            ->setPageTitle('detail', '图片元素详情')
            ->setHelp('index', '管理页面装修组件中的图片元素内容')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'subtitle', 'description'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from $this->getBasicFields();
        yield from $this->getTagFields($pageName);
        yield from $this->getSubscribeFields($pageName);
        yield from $this->getTimestampFields();
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('block', '所属组件'))
            ->add(TextFilter::new('title', '标题'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(BooleanFilter::new('loginJumpPage', '登录后跳转'))
        ;
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        $this->addFormEventListeners($formBuilder);

        return $formBuilder;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        $this->addFormEventListeners($formBuilder);

        return $formBuilder;
    }

    private function addFormEventListeners(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $this->handlePreSetData($event);
        });

        $formBuilder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $this->handlePreSubmitData($event);
        });
    }

    private function handlePreSetData(FormEvent $event): void
    {
        $entity = $event->getData();
        $form = $event->getForm();

        if (!$entity instanceof Element) {
            return;
        }

        $this->populateTagsString($entity, $form);
        $this->populateSubscribeTemplateIdsString($entity, $form);
    }

    private function populateTagsString(Element $entity, FormInterface $form): void
    {
        if (!$form->has('showTagsString')) {
            return;
        }

        $tags = $entity->getShowTags();
        $form->get('showTagsString')->setData(is_array($tags) ? implode(', ', $tags) : '');
    }

    private function populateSubscribeTemplateIdsString(Element $entity, FormInterface $form): void
    {
        if (!$form->has('subscribeTemplateIdsString')) {
            return;
        }

        $ids = $entity->getSubscribeTemplateIds();
        $form->get('subscribeTemplateIdsString')->setData([] !== $ids ? implode(', ', $ids) : '');
    }

    private function handlePreSubmitData(FormEvent $event): void
    {
        $data = $event->getData();

        if (!is_array($data)) {
            return;
        }

        /** @var array<string, mixed> $data */
        $data = $this->processShowTagsData($data);
        $data = $this->processSubscribeTemplateIdsData($data);

        $event->setData($data);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function processShowTagsData(array $data): array
    {
        if (!isset($data['showTagsString'])) {
            return $data;
        }

        $tagsString = $data['showTagsString'];
        assert(is_string($tagsString));
        $tags = '' !== $tagsString ? array_map('trim', explode(',', $tagsString)) : [];
        $data['showTags'] = $tags;
        unset($data['showTagsString']);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function processSubscribeTemplateIdsData(array $data): array
    {
        if (!isset($data['subscribeTemplateIdsString'])) {
            return $data;
        }

        $idsString = $data['subscribeTemplateIdsString'];
        assert(is_string($idsString));
        $ids = '' !== $idsString ? array_map('trim', explode(',', $idsString)) : [];
        $data['subscribeTemplateIds'] = $ids;
        unset($data['subscribeTemplateIdsString']);

        return $data;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getBasicFields()
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

        yield TextField::new('title', '标题')
            ->setRequired(true)
            ->setMaxLength(100)
        ;

        yield TextField::new('subtitle', '副标题')
            ->setMaxLength(100)
            ->hideOnIndex()
        ;

        yield TextareaField::new('description', '描述')
            ->hideOnIndex()
        ;

        yield ImageGalleryField::new('thumb1', '主图')
            ->setHelp('主图')
        ;

        yield ImageGalleryField::new('thumb2', '副图')
            ->setHelp('副图')->hideOnIndex()
        ;

        yield UrlField::new('path', '跳转链接')
            ->hideOnIndex()
        ;

        yield TextField::new('appId', '应用ID')
            ->setMaxLength(50)
            ->hideOnIndex()
        ;

        yield IntegerField::new('sortNumber', '排序')
            ->setHelp('数值越小排序越靠前')
        ;

        yield BooleanField::new('valid', '有效状态');

        yield BooleanField::new('loginJumpPage', '登录后跳转')
            ->hideOnIndex()
            ->setHelp('登录后是否跳转到指定页面')
        ;

        yield DateTimeField::new('beginTime', '开始时间')
            ->hideOnIndex()
        ;

        yield DateTimeField::new('endTime', '结束时间')
            ->hideOnIndex()
        ;

        yield TextareaField::new('showExpression', '显示规则')
            ->hideOnIndex()
            ->setHelp('控制元素显示的表达式规则')
        ;

        yield TextareaField::new('tracking', '跟踪代码')
            ->hideOnIndex()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTagFields(string $pageName)
    {
        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextareaField::new('showTagsDisplay', '显示标签')
                ->setFormTypeOptions([
                    'mapped' => false,
                ])
                ->formatValue(static function ($value, Element $entity): string {
                    $tags = $entity->getShowTags();

                    return is_array($tags) ? implode(', ', $tags) : '';
                })
                ->hideOnForm()
            ;
        }

        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            yield TextField::new('showTagsString', '显示标签')
                ->setFormTypeOptions([
                    'mapped' => false,
                ])
                ->setHelp('多个标签用逗号分隔')
            ;
        }
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getSubscribeFields(string $pageName)
    {
        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextareaField::new('subscribeTemplateIdsDisplay', '订阅模板ID')
                ->setFormTypeOptions([
                    'mapped' => false,
                ])
                ->formatValue(static function ($value, Element $entity): string {
                    $ids = $entity->getSubscribeTemplateIds();

                    return [] !== $ids ? implode(', ', $ids) : '';
                })
                ->hideOnForm()
            ;
        }

        if (Crud::PAGE_EDIT === $pageName || Crud::PAGE_NEW === $pageName) {
            yield TextField::new('subscribeTemplateIdsString', '订阅模板ID')
                ->setFormTypeOptions([
                    'mapped' => false,
                ])
                ->setHelp('多个ID用逗号分隔')
            ;
        }
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTimestampFields()
    {
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }
}
