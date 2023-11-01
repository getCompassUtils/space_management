<?php declare(strict_types=1);

namespace Tariff\Plan\MemberCount;

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
		public ?OptionExtendPolicy   $option_extend_policy,
		public ?OptionLimit          $option_limit,
		public ?OptionRestrictPolicy $option_restrict_policy,
	) {

		// объект-структура
	}
}
