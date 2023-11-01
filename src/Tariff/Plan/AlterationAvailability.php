<?php declare(strict_types=1);

namespace Tariff\Plan;

/**
 * Описывает данные доступности элемента витрины.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
class AlterationAvailability {

	public const AVAILABLE_DETACHED         = "detached";                    // можно активировать в любой момент
	public const AVAILABLE_REASON_REQUIRED  = "reason_required";             // требуется основание (промо или платеж)
	public const AVAILABLE_WHILE_TRIAL      = "while_trial";                 // требуется основание (промо или платеж)
	public const AVAILABLE_FREE             = "free";                        // безусловно бесплатное
	public const UNAVAILABLE_UNAPPROPRIATED = "unavailable_unappropriated";  // данные для активации не сходятся
	public const UNAVAILABLE_OUTDATED       = "unavailable_outdated";        // срок действия истек
	public const UNAVAILABLE_SAME           = "unavailable_same";            // значения идентичны

	// список статусов, при котором элемент считается доступным
	protected const _AVAILABLE_AVAILABILITY_LIST = [
		self::AVAILABLE_DETACHED,
		self::AVAILABLE_REASON_REQUIRED,
		self::AVAILABLE_FREE,
		self::AVAILABLE_WHILE_TRIAL,
	];

	// список приоритетов, переход с более
	// приоритетного на менее приоритетный невозможен
	protected const _AVAILABILITY_PRIORITY_LIST = [
		self::AVAILABLE_DETACHED,
		self::AVAILABLE_REASON_REQUIRED,
		self::AVAILABLE_WHILE_TRIAL,
		self::AVAILABLE_FREE,
		self::UNAVAILABLE_UNAPPROPRIATED,
		self::UNAVAILABLE_OUTDATED,
		self::UNAVAILABLE_SAME,
	];

	protected static ?array $_flipped_priority_list = null;

	/**
	 * Конструктор.
	 */
	public function __construct(
		public string    $availability,
		protected int    $_unavailable_code = 0,
		protected string $_unavailable_message = "",
	) {

		if (is_null(static::$_flipped_priority_list)) {
			static::$_flipped_priority_list = array_flip(static::_AVAILABILITY_PRIORITY_LIST);
		}

		if ($_unavailable_code !== 0 && $this->isAvailable()) {
			throw new \LogicException("unavailable code required");
		}

		self::assertAvailability($availability);
	}

	/**
	 * Валидирует статус доступности.
	 */
	public static function assertAvailability(string $availability):void {

		if (!isset(static::$_flipped_priority_list[$availability])) {
			throw new \InvalidArgumentException("passed bad availability: $availability");
		}
	}

	/**
	 * Объединяет несколько объектов в один с учетом правил перехода доступности.
	 */
	public function arrange(self ...$to_merge_list):static {

		foreach ($to_merge_list as $to_merge) {

			// проверяем, что новый уровень доступности выше указанного
			if (static::$_flipped_priority_list[$to_merge->availability] > static::$_flipped_priority_list[$this->availability]) {

				$this->availability         = $to_merge->availability;
				$this->_unavailable_code    = $to_merge->_unavailable_code;
				$this->_unavailable_message = $to_merge->_unavailable_message;
			}
		}

		return $this;
	}

	/**
	 * Возвращает статус доступности элемента.
	 */
	public function isAvailable():bool {

		return in_array($this->availability, static::_AVAILABLE_AVAILABILITY_LIST, true);
	}

	public function isReasonRequired():bool {

		return $this->availability === static::AVAILABLE_REASON_REQUIRED;
	}

	/**
	 * Возвращает код ошибки.
	 */
	public function getCode():int {

		return $this->_unavailable_code;
	}

	/**
	 * Возвращает сопровождающее сообщение.
	 */
	public function getMessage():string {

		return $this->_unavailable_message;
	}
}
