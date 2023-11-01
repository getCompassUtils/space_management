<?php declare(strict_types=1);

namespace Tariff\Plan;

/**
 * Данные для сохранения или экспорта тарифного плана.
 */
#[\JetBrains\PhpStorm\Immutable]
abstract class BaseSaveData {

	// тип плана выносим отдельно, чтобы никто случайно
	// не задал новую новое значение у уже существующего
	// плана, и не началась путаница с загрузкой
	public int $plan_type = 0;

	/**
	 * Конструктор.
	 */
	public function __construct(

		public int   $plan_id,
		public int   $valid_till,
		public int   $active_till,
		public int   $free_active_till,
		public array $option_list,
	) {

		// ничего не делаем, просто класс-структура
	}
}