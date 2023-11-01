<?php declare(strict_types=1);

namespace Tariff\Plan;

/**
 * Описательная структура для генерации действия в будущем.
 * Как правило должна привязываться к goods_id для последующей активации.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
abstract class BaseAlteration {

	// эффект альтерации — смена каких-либо параметров плана
	// например изменение числа участников, не касается длительности тарифа
	public const CHANGE = 10;

	// действия — продление базовой длительности плана
	// если альтерация должна продлить план, то необходимо указать это действие
	public const PROLONG = 20;

	// особое действие, которое связано с активацией плана
	// обычно достаточно продления/смены, но иногда активация требуется как дополнительный флаг
	public const ACTIVATE = 30;

	// при продлении время действия будет добавлено к max(time(), active_till)
	// стандартный способ продления, используется в большинстве случаев
	public const PROLONGATION_RULE_EXTEND = 10;

	// при продление будет установлено указанное время действия
	// с помощью этого правила можно, например, остановить текущий план, указав текущую дату
	public const PROLONGATION_RULE_SET = 20;

	// устанавливает бесконечную длительность тарифному плану
	// для каких-то особых случаев, обычно не должно использоваться
	public const PROLONGATION_RULE_INFINITE = 30;

	// альтерация не осуществляет продление
	// поведение любой альтерации по умолчанию,
	// если продление тарифа не планируется, то нужно его использовать
	public const PROLONGATION_RULE_NONE = 90;

	// бесплатно во время пробного периода
	// если альтерация может быть свободно применена во время
	// пробного периода, то она должна перенимать это поведение
	public const TRIAL_BEHAVIOUR_FREE_WHILE_ACTIVE = 10;

	// доступно к бесплатной активации, пока пробный период еще не начинался
	// как правило работает для альтераций, которыми можно начать пробный период без оплаты
	public const TRIAL_BEHAVIOUR_FREE_WHILE_AVAILABLE = 20;

	// разрешено, пока пробный период доступен
	// для всяких штук, которые требуют наличия неиспользованного пробного периода;
	// например, автоматическое расширение для числа участников при вступлении нового пользователя
	public const TRIAL_BEHAVIOUR_ALLOWED_WHILE_AVAILABLE = 30;

	// правило для проверки текущего срока действия
	// с полным совпадением с указанным значением
	public const EXPECTED_ACTIVE_TILL_RULE_STRICT = 10;

	// правило для проверки текущего срока действия
	// с приблизительным совпадением (плюс-минус дельта даты для указанного значения)
	public const EXPECTED_ACTIVE_TILL_RULE_RANGE = 20;

	// проверка по длительности не требуется
	// поведение правила по умолчанию
	public const EXPECTED_ACTIVE_TILL_RULE_NONE = 90;

	/** @var int[] список доступных правил продления */
	protected const _ALLOWED_PROLONGATION_RULE_LIST = [
		self::PROLONGATION_RULE_EXTEND,
		self::PROLONGATION_RULE_SET,
		self::PROLONGATION_RULE_INFINITE,
		self::PROLONGATION_RULE_NONE,
	];

	/** @var int[] список доступных действий */
	protected const _ALLOWED_ACTION_LIST = [
		self::CHANGE,
		self::ACTIVATE,
		self::PROLONG,
	];

	/** @var int[] список доступных моделей поведения для пробного периода */
	protected const _ALLOWED_TRIAL_BEHAVIOUR_LIST = [
		self::TRIAL_BEHAVIOUR_FREE_WHILE_ACTIVE,
		self::TRIAL_BEHAVIOUR_FREE_WHILE_AVAILABLE,
		self::TRIAL_BEHAVIOUR_ALLOWED_WHILE_AVAILABLE,
	];

	// уровень доступности можно установить один раз, дальше только изменять
	protected bool $_is_availability_set = false;

	public array $action_list          = [self::PROLONG];
	public array $trial_behaviour_list = [];
	public int   $prolongation_rule    = self::PROLONGATION_RULE_NONE;
	public int   $prolongation_value   = 0;
	public bool  $is_free_while_trial  = false;
	public int $active_till_rule      = self::EXPECTED_ACTIVE_TILL_RULE_NONE;
	public int $active_till_min_bound = 0;
	public int $active_till_max_bound = 0;

	// уровень доступности альтерации
	public AlterationAvailability $availability;

	/** @var callable[] */
	public array $_extra_conditions = [];

	/**
	 * Закрываем конструктор.
	 */
	protected function __construct() {

		// по умолчанию альтерация всегда недоступна
		$this->availability = new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED);
	}

	/**
	 * Статический конструктор
	 */
	public static function make():static {

		return new static();
	}

	/**
	 * Устанавливает набор совершаемых действий.
	 * Одновременно может быть установленно несколько действий
	 *
	 * Альтерация может совершать следующие действия:
	 *    — изменение
	 *    — продление
	 *    — активация
	 *
	 * @see CHANGE
	 * @see PROLONG
	 * @see ACTIVATE
	 */
	public function setActions(int ...$action_list):static {

		$this->action_list = $action_list;
		return $this;
	}

	/**
	 * Добавляет действие к списку.
	 *
	 * Альтерация может совершать следующие действия:
	 *    — изменение
	 *    — продление
	 *    — активация
	 *
	 * @see CHANGE
	 * @see PROLONG
	 * @see ACTIVATE
	 */
	public function addAction(int $action):static {

		$this->action_list = array_unique([$action, ...$this->action_list]);
		return $this;
	}

	/**
	 * Устанавливает правило продление срока действия.
	 * Если установлено, то альтерация должна продлевать срок действия тарифного плана.
	 *
	 * @see PROLONGATION_RULE_EXTEND
	 * @see PROLONGATION_RULE_SET
	 * @see PROLONGATION_RULE_INFINITE
	 * @see PROLONGATION_RULE_NONE
	 */
	public function setProlongation(int $rule, int $value = 0):static {

		$this->prolongation_rule  = $rule;
		$this->prolongation_value = $value;

		return $this;
	}

	/**
	 * Устанавливает флаг поведения для пробного периода.
	 * По умолчанию никаких особых правил нет и альтерация не аффектится пробным периодом.
	 *
	 * @see TRIAL_BEHAVIOUR_FREE_WHILE_ACTIVE
	 * @see TRIAL_BEHAVIOUR_FREE_WHILE_AVAILABLE
	 * @see TRIAL_BEHAVIOUR_ALLOWED_WHILE_AVAILABLE
	 */
	public function setTrialBehaviour(int ...$behaviour_list):static {

		$this->trial_behaviour_list = $behaviour_list;
		return $this;
	}

	/**
	 * Устанавливает флаг бесплатности во время пробного периода.
	 * @deprecated use setTrialBehaviour
	 */
	public function setIsFreeWhileTrial():static {

		$this->setTrialBehaviour(static::TRIAL_BEHAVIOUR_FREE_WHILE_ACTIVE);
		return $this;
	}

	/**
	 * Устанавливает доступность элемента по умолчанию.
	 *
	 * Эту настройку можно задать только один раз,
	 * по умолчанию альтерация имеет доступность «Недоступно»
	 */
	public function setAvailability(AlterationAvailability $availability):static {

		if ($this->_is_availability_set) {
			throw new \RuntimeException("availability already set");
		}

		$this->availability         = $availability;
		$this->_is_availability_set = true;

		return $this;
	}

	/**
	 * Устанавливает ожидаемую дату истечения для активации.
	 *
	 * Позволяет избегать ситуаций, когда делается change-действие,
	 * но указывается некорректная длительность.
	 */
	public function setExpectedActiveTill(int $expected_active_till_rule, int $value = 0, int $delta = 0):static {

		$this->active_till_rule = $expected_active_till_rule;

		switch ($this->active_till_rule) {

			case static::EXPECTED_ACTIVE_TILL_RULE_STRICT:

				$this->active_till_min_bound = $value;
				$this->active_till_max_bound = $value;
				break;
			case static::EXPECTED_ACTIVE_TILL_RULE_RANGE:

				$this->active_till_min_bound = $value - $delta;
				$this->active_till_max_bound = $value + $delta;
				break;
		}

		return $this;
	}

	/**
	 * Проверяет, является ли действие активацией.
	 */
	public function isActivation():bool {

		return in_array($this::ACTIVATE, $this->action_list, true);
	}

	/**
	 * Проверяет, является ли действие изменением тарифного плана.
	 */
	public function isChange():bool {

		return in_array($this::CHANGE, $this->action_list, true);
	}

	/**
	 * Проверяет, является ли действие продлением.
	 */
	public function isProlongation():bool {

		return in_array($this::PROLONG, $this->action_list, true);
	}

	/**
	 * Проверяет является ли правило пролонгации расширяющей.
	 */
	public function isProlongationExtend():bool {

		return $this->isProlongation() && $this->prolongation_rule === static::PROLONGATION_RULE_EXTEND;
	}

	/**
	 * Проверяет является ли пролонгация установкой конкретного значения.
	 */
	public function isProlongationSet():bool {

		return $this->isProlongation() && $this->prolongation_rule === static::PROLONGATION_RULE_SET;
	}

	/**
	 * Проверяет является ли пролонгация установление бесконечной длительности.
	 */
	public function isProlongationInfinite():bool {

		return $this->isProlongation() && $this->prolongation_rule === static::PROLONGATION_RULE_INFINITE;
	}

	/**
	 * Проверяет наличие строго требования по дате действия плана.
	 */
	public function isStrictActiveTillRequired():bool {

		return $this->active_till_rule === static::EXPECTED_ACTIVE_TILL_RULE_STRICT;
	}

	/**
	 * Проверяет наличие относительного требования по дате действия плана.
	 */
	public function isRangeActiveTillRequired():bool {

		return $this->active_till_rule === static::EXPECTED_ACTIVE_TILL_RULE_RANGE;
	}

	/**
	 * Проверяет возможность бесплатной активации во время пробного периода.
	 */
	public function isFreeOnActiveTrial():bool {

		return in_array(static::TRIAL_BEHAVIOUR_FREE_WHILE_ACTIVE, $this->trial_behaviour_list, true);
	}

	/**
	 * Проверяет возможность бесплатной активации при наличии неизрасходованного пробного периода.
	 */
	public function isFreeOnActiveAvailable():bool {

		return in_array(static::TRIAL_BEHAVIOUR_FREE_WHILE_AVAILABLE, $this->trial_behaviour_list, true);
	}

	/**
	 * Проверяет необходимость наличия пробного периода для применения альтерации.
	 */
	public function isActiveTrialRequired():bool {

		return in_array(static::TRIAL_BEHAVIOUR_ALLOWED_WHILE_AVAILABLE, $this->trial_behaviour_list, true);
	}
}
