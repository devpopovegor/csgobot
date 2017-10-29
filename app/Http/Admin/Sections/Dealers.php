<?php

namespace App\Http\Admin\Sections;

use App\Dealer;
use App\User;
use AdminDisplay;
use AdminColumn;
use AdminForm;
use AdminFormElement;
use Illuminate\Support\Facades\Auth;
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
class Dealers extends Section implements Initializable
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
        return AdminDisplay::table()
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns(
                AdminColumn::text('id', '#')->setWidth('30px'),
                AdminColumn::link('username', 'Username'),
                AdminColumn::datetime('start_subscription', 'Начало подписки')->setFormat('d.m.Y')->setWidth('150px'),
                AdminColumn::datetime('end_subscription', 'Конец подписки')->setFormat('d.m.Y')->setWidth('150px')
            )->paginate(20);
    }

    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
        return AdminForm::panel()->addBody([
            AdminFormElement::text('first_name', 'Имя'),
            AdminFormElement::text('last_name', 'Фамилия'),
            AdminFormElement::text('username', 'Username')->required(),
            AdminFormElement::checkbox('subscription', 'Подписка'),
            AdminFormElement::date('start_subscription', 'Начало подписки'),
            AdminFormElement::date('end_subscription', 'Конец подписки'),
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
            return Dealer::count();
        });
    }

    public function getIcon()
    {
        return 'fa fa-group';
    }

    public function getTitle()
    {
        return 'Клиенты';
    }

    public function getCreateTitle()
    {
        return 'Добавление клиента';
    }

    public function getEditTitle()
    {
        return 'Редактирование клиента';
    }

}