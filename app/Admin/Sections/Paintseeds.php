<?php

namespace App\Admin\Sections;

use App\Paintseed;
use AdminDisplay;
use AdminDisplayFilter;
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
class Paintseeds extends Section implements Initializable
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
                           ->setFilters(AdminDisplayFilter::custom('custom_filter')->setCallback(function($query, $value) {
                               $query->where('value', $value);
                           }))
		                   ->setHtmlAttribute('class', 'table-primary')
		                   ->setColumns(
			                   AdminColumn::relatedLink('item.full_name', 'Предмет'),
			                   AdminColumn::text('steam', 'Steam'),
			                   AdminColumn::text('float', 'Флоат'),
                               AdminColumn::text('value', 'Паттерн')->setOrderable('value'),
                               AdminColumn::text('pattern_name', 'НП')
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
			AdminFormElement::text('item_id', 'steam id'),
			AdminFormElement::text('value', 'Паттерн'),
			AdminFormElement::text('name', 'Название'),
			AdminFormElement::text('steam_id', 'id предмета')
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
		return false;
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
			return Paintseed::count();
		});
	}

	public function getIcon()
	{
		return 'fa fa-list-ul';
	}

	public function getTitle()
	{
		return 'Все паттерны';
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