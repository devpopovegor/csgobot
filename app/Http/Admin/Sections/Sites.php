<?php

namespace App\Http\Admin\Sections;

use App\Dealer;
use App\Site;
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
class Sites extends Section implements Initializable
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
			                   AdminColumn::link('url', 'Ссылка'),
			                   AdminColumn::text('get_data', 'Ссылка получения данных'),
			                   AdminColumn::text('active', 'Активность')
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
			AdminFormElement::text('url', 'Ссылка'),
			AdminFormElement::text('get_data', 'Ссылка получения данных'),
			AdminFormElement::checkbox('active', 'Активность'),
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
			return Site::count();
		});
	}

	public function getIcon()
	{
		return 'fa fa-globe';
	}

	public function getTitle()
	{
		return 'Сайты';
	}

	public function getCreateTitle()
	{
		return 'Добавление сайта';
	}

	public function getEditTitle()
	{
		return 'Редактирование сайта';
	}

}