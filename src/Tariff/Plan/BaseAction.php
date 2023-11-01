<?php declare(strict_types=1);

namespace Tariff\Plan;

/**
 * Класс описывающий состояние какое-либо действие, совершаемое с тарифом.
 * Например предварительно получив goods_id можно активировать покупку пользователя.
 *
 * По сути просто набор флагов, на основании которого тарифный план и его
 * опции могу составить стратегия поведения в текущем окружении.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
abstract class BaseAction {

	// выполнение действия с помощью оплаты, обеспечивает основание для альтерации
	// используется, когда исходный запрос на альтерацию приходит с биллинга
	public const METHOD_PAYMENT = 10;

	// выполнение действия с промо, обеспечивает основание для альтерации
	// выполняется, когда активация проводится с помощью промо-акции
	public const METHOD_PROMO = 20;

	// выполнение действия на yolo, не является основанием
	// обычно используется, при активации товара с клиента, но без связанной промо-акции
	// например — уменьшение лимита пользователей в пространстве
	public const METHOD_DETACHED = 30;

	// выполнение действия в обязательном режиме
	// игнорирует все правила тарифа и жестко переводит в нужное состояние
	// в стандартных сценариях использовать не должно
	public const METHOD_FORCE = 90;

	// пробный период — недоступен вообще или уже израсходован
	public const TRIAL_STATE_NONE = 10;

	// пробный период доступен для активации, но в данный момент неактивен
	public const TRIAL_STATE_AVAILABLE = 20;

	// пробный период активен в данный момент
	public const TRIAL_STATE_ACTIVE = 30;

	/** @var int[] список доступных методов активации */
	protected const _ALLOWED_METHOD_LIST = [
		self::METHOD_PAYMENT,
		self::METHOD_PROMO,
		self::METHOD_DETACHED,
		self::METHOD_FORCE,
	];

	/** @var int[] список методов активации, подразумевающих наличие основания */
	protected const _METHOD_REASON_LIST = [
		self::METHOD_PROMO,
		self::METHOD_PAYMENT,
	];

	/**
	 * Конструктор.
	 */
	public function __construct(
		protected int         $_method,
		public BaseAlteration $alteration,
		public int            $_trial_state,
		public bool           $_is_active,
		protected int         $_time
	) {

		// nothing
	}

	/**
	 * Возвращает время выполнения действия.
	 */
	public function getTime():int {

		return $this->_time;
	}

	/**
	 * Проверяет, выполняется ли действия с помощью оплаты.
	 */
	public function isPayment():bool {

		return $this->_method === static::METHOD_PAYMENT;
	}

	/**
	 * Проверяет, выполняется ли действия с помощью промо.
	 */
	public function isPromo():bool {

		return $this->_method === static::METHOD_PROMO;
	}

	/**
	 * Есть ли у действия основание в виде платежа или промо.
	 */
	public function hasReason():bool {

		return in_array($this->_method, static::_METHOD_REASON_LIST, true);
	}

	/**
	 * Проверяет, выполняется ли действия в принудительном режиме.
	 */
	public function isForced():bool {

		return $this->_method === static::METHOD_FORCE;
	}

	/**
	 * Возвращает наличие активного пробного периода на момент совершения действия.
	 */
	public function isTrialActive():bool {

		return $this->_trial_state === static::TRIAL_STATE_ACTIVE;
	}

	/**
	 * Возвращает наличие доступного для активации периода на момент совершения действия.
	 */
	public function isTrialAvailable():bool {

		return $this->_trial_state === static::TRIAL_STATE_AVAILABLE;
	}

	/**
	 * Возвращает флаг активности тарифна на момент совершения действия.
	 */
	public function isActive():bool {

		return $this->_is_active;
	}
}
