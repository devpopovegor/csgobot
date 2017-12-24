<?php

namespace App\Admin\Sections;

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
class Users extends Section implements Initializable
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
                AdminColumn::link('name', 'Имя'),
                AdminColumn::text('email', 'E-mail')
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
            AdminFormElement::text('name', 'Имя')->required(),
            AdminFormElement::text('email', 'E-mail')->required(),
            AdminFormElement::text('password', 'Пароль')->required()
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
        if (Auth::user()->hasRole('admin')) return true;
        else return false;
    }

    public function isEditable(\Illuminate\Database\Eloquent\Model $model)
    {
        if (Auth::user()->hasRole('admin')) return true;
        else return false;
    }

    public function isCreatable()
    {
        if (Auth::user()->hasRole('admin')) return true;
        else return false;
    }

    public function isDisplayable()
    {
        if (Auth::user()->hasRole('admin')) return true;
        else return false;
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
            return User::count();
        });
    }

    public function getIcon()
    {
        return 'fa fa-group';
    }

    public function getTitle()
    {
        return 'Пользователи';
    }
}