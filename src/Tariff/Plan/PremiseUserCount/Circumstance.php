<?php declare(strict_types=1);

namespace Tariff\Plan\PremiseUserCount;

/**
 * Класс для обмена данными с тарифным планом числа участников.
 */
#[\JetBrains\PhpStorm\Immutable]
class Circumstance extends \Tariff\Plan\BaseCircumstance {

	/**
	 * Инициализирует Dto-структуру для работы с тарифным планом.
	 *
	 * @param int $current_user_count текущее число пользователя компании.
	 */
	public function __construct(
		public int $current_user_count,
	) {

		// пусто, просто объект-структура
	}
}