<?php

namespace App\Admin\Sections;

use App\Pattern;
use AdminDisplay;
use AdminColumn;
use AdminForm;
use AdminFormElement;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Section;


/**
 * Class Users
 *
 * @property \App\User $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class Patterns extends Section implements Initializable
{
    protected $model;
    /**
     * @see http://sleepingowladmin.ru/docs/model_configuration#ограничение-прав-доступа
     *
     * @var bool
     */
    protected $checkAccess = false;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @return DisplayInterface
     */
    public function onDisplay()
    {
        return AdminDisplay::datatables()
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns(
//                AdminColumn::text('id', '#')->setWidth('30px'),
                AdminColumn::relatedLink('item.full_name', 'Номер предмета'),
                AdminColumn::text('name', 'Название'),
                AdminColumn::text('value', 'Паттерн')
            )->setDisplaySearch(true)->paginate(100);
    }

    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
        return AdminForm::panel()->addBody([
            AdminFormElement::text('item_id', 'Номер предмета'),
            AdminFormElement::text('name', 'Название'),
            AdminFormElement::text('value', 'Паттерн')
        ]);
    }

    /**
     * @return FormInterface
     */
    public function onCreate()
    {
        return $this->onEdit(null);
    }

    public function isDeletable(\Illuminate\Database\Eloquent\Model $model)
    {
        return true;
    }

    public function isEditable(\Illuminate\Database\Eloquent\Model $model)
    {
        return true;
    }

    public function isCreatable()
    {
        return true;
    }

    public function isDisplayable()
    {
        return true;
    }

    /**
     * @return void
     */
    public function onDelete($id)
    {
        // remove if unused
    }

    /**
     * @return void
     */
    public function onRestore($id)
    {
        // remove if unused
    }

    /**
     * Initialize class.
     */
    public function initialize()
    {
        $this->addToNavigation($priority = 500, function() {
            return Pattern::count();
        });
    }

    public function getIcon()
    {
        return 'fa fa-list-ul';
    }

    public function getTitle()
    {
        return 'Паттерны';
    }

    public function getCreateTitle()
    {
        return 'Добавление паттерна';
    }

    public function getEditTitle()
    {
        return 'Редактирование паттерна';
    }

}