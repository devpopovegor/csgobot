<?php

namespace App\Http\Admin\Sections;

use App\Dealer;
use App\Item;
use App\Steam;
use App\Task;
use App\User;
use AdminDisplay;
use AdminColumn;
use AdminColumnFilter;
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
class Steams extends Section implements Initializable
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
                AdminColumn::text('steam_id', 'Steam id'),
                AdminColumn::text('task_id', 'id поиска')
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
            AdminColumn::text('steam_id', 'Steam id'),
            AdminColumn::text('task_id', 'id задачи')
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
//        if (Auth::user()->hasRole('admin')) return true;
//        else return false;
        return true;
    }

    public function isEditable(\Illuminate\Database\Eloquent\Model $model)
    {
        return false;
    }

    public function isCreatable()
    {
        return false;
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
            return Steam::count();
        });
    }

    public function getIcon()
    {
        return 'fa fa-list-ul';
    }

    public function getTitle()
    {
        return 'Поиски - steam id';
    }

}