<?php declare(strict_types=1);

namespace Tariff\Plan\MemberCount;

/**
 * Класс для обмена данными с тарифным планом числа участников.
 */
#[\JetBrains\PhpStorm\Immutable]
class Circumstance extends \Tariff\Plan\BaseCircumstance {

	/**
	 * Инициализирует Dto-структуру для работы с тарифным планом.
	 *
	 * @param int $current_member_count текущее число участников компании.
	 * @param int $postpayment_period период постоплаты
	 */
	public function __construct(
		public int $current_member_count,
		public int $postpayment_period,
	) {

		// пусто, просто объект-структура
	}
}