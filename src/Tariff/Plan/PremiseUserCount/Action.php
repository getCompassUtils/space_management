<?php declare(strict_types=1);

namespace Tariff\Plan\PremiseUserCount;

/**
 * Класс, описывающий действие при применении альтерации.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
class Action extends \Tariff\Plan\BaseAction {

	// план активен, потому что указана бесконечная длительность
	// такое происходит в состоянии по умолчанию (или если задать руками)
	public const ACTIVE_STATE_INFINITE = 10;

	// причина активности — план в данный момент имеет
	// дату окончания и она еще не наступила на момент исполнения
	public const ACTIVE_STATE_NOT_EXPIRED = 20;

	// в данный момент план активен
	// потому что опция лимит позволяет бесплатность
	public const ACTIVE_STATE_FREE_LIMIT = 30;

	// план в данный момент не активен — у него указана
	//  дата истечения и она наступила на момент исполнения
	public const ACTIVE_STATE_NONE = 90;

	/** @var int[] состояния активности, при которых план считайся действующим */
	protected const _ACTING_ACTIVE_STATE_LIST = [
		self::ACTIVE_STATE_NOT_EXPIRED,
		self::ACTIVE_STATE_FREE_LIMIT,
		self::ACTIVE_STATE_NONE,
	];

	protected array $_active_state_list = [self::ACTIVE_STATE_NONE];

	/**
	 * Конструктор.
	 */
	public function __construct(int $_method, Alteration $alteration, int $_trial_state, array $active_state_list, int $_time) {

		$this->_active_state_list = $active_state_list;
		$is_active                = count(array_intersect($active_state_list, static::_ACTING_ACTIVE_STATE_LIST)) > 0;

		parent::__construct($_method, $alteration, $_trial_state, $is_active, $_time);
	}

	/**
	 * Возвращает флаг актуального срока действия плана на момент исполнения.
	 */
	public function isActiveOnNotExpired():bool {

		return $this->isActive() && in_array(static::ACTIVE_STATE_NOT_EXPIRED, $this->_active_state_list, true);
	}

	/**
	 * Возвращает флаг бесконечного срока действия плана на момент исполнения.
	 */
	public function isActiveOnInfinite():bool {

		return $this->isActive() && in_array(static::ACTIVE_STATE_INFINITE, $this->_active_state_list, true);
	}

	/**
	 *  Возвращает флаг бесплатного ограничения лимита действия плана на момент исполнения.
	 */
	public function isActiveOnFreeLimit():bool {

		return $this->isActive() && in_array(static::ACTIVE_STATE_FREE_LIMIT, $this->_active_state_list, true);
	}
}