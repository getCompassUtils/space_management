<?php declare(strict_types=1);

namespace Tariff\Plan\PremiseUserCount;

/**
 * Описывает ожидаемый результат выполнения действия.
 */
#[\JetBrains\PhpStorm\Immutable]
class Dynamic {

	/**
	 * Конструктор
	 */
	public function __construct(
		public int                   $active_till,
		public int                   $free_active_till,
		public ?OptionLimit          $option_limit,
		public ?OptionRestrictPolicy $option_restrict_policy,
		public ?OptionDemo           $option_demo
	) {

		// объект-структура
	}
}
