<?php
namespace Vspace\Optimization\DataProviders\Entities;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

/**
 * Class OptionTable
 * 
 * Fields:
 * <ul>
 * <li> MODULE_ID string(50) mandatory
 * <li> NAME string(100) mandatory
 * <li> VALUE text optional
 * <li> DESCRIPTION string(255) optional
 * <li> SITE_ID string(2) optional
 * </ul>
 *
 * @package Bitrix\Option
 **/

class OptionTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_option';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new StringField(
				'MODULE_ID',
				[
					'primary' => true,
					'validation' => function()
					{
						return[
							new LengthValidator(null, 50),
						];
					},
					'title' => Loc::getMessage('OPTION_ENTITY_MODULE_ID_FIELD'),
				]
			),
			new StringField(
				'NAME',
				[
					'primary' => true,
					'validation' => function()
					{
						return[
							new LengthValidator(null, 100),
						];
					},
					'title' => Loc::getMessage('OPTION_ENTITY_NAME_FIELD'),
				]
			),
			new TextField(
				'VALUE',
				[
					'title' => Loc::getMessage('OPTION_ENTITY_VALUE_FIELD'),
				]
			),
			new StringField(
				'DESCRIPTION',
				[
					'validation' => function()
					{
						return[
							new LengthValidator(null, 255),
						];
					},
					'title' => Loc::getMessage('OPTION_ENTITY_DESCRIPTION_FIELD'),
				]
			),
			new StringField(
				'SITE_ID',
				[
					'validation' => function()
					{
						return[
							new LengthValidator(null, 2),
						];
					},
					'title' => Loc::getMessage('OPTION_ENTITY_SITE_ID_FIELD'),
				]
			),
		];
	}
}