<?php declare(strict_types=1);

namespace Tariff\Plan\PremiseUserCount;

use Tariff\Plan\AlterationAvailability;
use Tariff\Plan\BaseAction;
use Tariff\Plan\BaseAlteration;

/**
 * Тарифный план числа пользователей,
 * доступных в пространстве по умолчанию.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
abstract class BasePlan extends \Tariff\Plan\BasePlan implements PremiseUserCount {

	/** @var int уникальный идентификатор плана */
	public const PLAN_ID = 2000;

	/** @var int уникальный идентификатор плана */
	public const PLAN_TYPE = 10;

	protected const _LIMIT_OPTION_KEY           = OptionLimit::OPTION_NAME;
	protected const _RESTRICT_POLICY_OPTION_KEY = OptionRestrictPolicy::OPTION_NAME;
	protected const _DEMO_OPTION_KEY            = OptionDemo::OPTION_NAME;

	public const ERROR_PLAN_WAS_CHANGED                = 200_00_0_10; // план не соответствует ожидаемым параметрам
	public const ERROR_PLAN_UNEXPECTED_EXPIRATION_DATE = 200_00_0_11; // срок действия плана не соответствует ожидаемому

	/** @var array настройки опций по умолчанию */
	protected const _DEFAULT_OPTION_DATA = [

		// настройка числа пользователей
		self::_LIMIT_OPTION_KEY           => [
			"value" => OptionLimit::LIMIT_10
		],

		// настройка политики расширения
		self::_RESTRICT_POLICY_OPTION_KEY => [
			"active_from" => 0
		],

		// настройка опции демо-периода
		self::_DEMO_OPTION_KEY            => [
			"active_till" => 0
		],
	];

	/** @var int срок действия плана */
	protected int $_active_till;

	/** @var int срок действия плана */
	protected int $_free_active_till;

	protected OptionLimit          $_option_limit;           // ограничение числа участников
	protected OptionRestrictPolicy $_option_restrict_policy; // правила ограничения доступа к пространству
	protected OptionDemo           $_option_demo;            // опция состояния демо-периода

	#[\JetBrains\PhpStorm\Immutable]
	protected Dynamic $_original_dynamic; // исходное состояние, если он изменится, то данные тарифного плана нужно сохранить
	protected Dynamic $_last_dynamic;     // состояние тарифа после последнего изменения

	/**
	 * Закрываем конструктор.
	 * Для создания используем только статические конструкторы.
	 */
	protected function __construct(
		int                  $active_till,
		int                  $free_active_till,
		OptionLimit          $option_limit,
		OptionRestrictPolicy $option_restrict_policy,
		OptionDemo           $option_demo,
	) {

		// записываем дату действия
		$this->_active_till      = $active_till;
		$this->_free_active_till = $free_active_till;

		// генерируем нужные опции
		$this->_option_limit           = $option_limit;
		$this->_option_restrict_policy = $option_restrict_policy;
		$this->_option_demo            = $option_demo;

		// фиксируем исходные данные тарифа
		$this->_original_dynamic = new Dynamic(
			$active_till,
			$free_active_till,
			$this->_option_limit,
			$this->_option_restrict_policy,
			$this->_option_demo
		);

		$this->_last_dynamic = $this->_original_dynamic;
	}

	/**
	 * @inheritDoc
	 */
	public function _hasOptionChanges():bool {

		$is_same = $this->_option_limit->isSame($this->_original_dynamic->option_limit)
			&& $this->_option_restrict_policy->isSame($this->_original_dynamic->option_restrict_policy)
			&& $this->_option_demo->isSame($this->_original_dynamic->option_restrict_policy);

		return !$is_same;
	}

	/**
	 * @inheritDoc
	 */
	public function getData():SaveData {

		return new SaveData(...$this->_getSaveData());
	}

	/**
	 * @inheritDoc
	 */
	#[\JetBrains\PhpStorm\ArrayShape([self::_LIMIT_OPTION_KEY => "int[]", self::_RESTRICT_POLICY_OPTION_KEY => "int[]", self::_DEMO_OPTION_KEY => "int[]"])]
	public function _getOptionSaveData():array {

		return [
			static::_LIMIT_OPTION_KEY           => $this->_option_limit->export(),
			static::_RESTRICT_POLICY_OPTION_KEY => $this->_option_restrict_policy->export(),
			static::_DEMO_OPTION_KEY            => $this->_option_demo->export(),
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getLimit():int {

		return $this->_option_limit->getValue();
	}

	/**
	 * @inheritDoc
	 */
	public function isActive(int $relative_date):bool {

		return $this->_active_till === 0 || $this->_active_till > $relative_date || $this->isFree($relative_date);
	}

	/**
	 * Проверяет, соответствует ли план указанному состоянию окружения.
	 */
	public function isFit(Circumstance $circumstance):bool {

		return $this->_option_limit->isFit($circumstance);
	}

	/**
	 * Проверяет, не накладывает ли тариф ограничений.
	 */
	public function isRestricted(int $time, int $delta = 0):bool {

		return $this->_option_restrict_policy->isRestricted($time, $delta);
	}

	/**
	 * @inheritDoc
	 */
	public function isFree(int $time):bool {

		return $this->_active_till === 0 || $this->_option_limit->isFree();
	}

	# region методы изменения

	/**
	 * Делает правила альтерации более строгими.
	 */
	public function arrangeAlteration(Alteration $alteration, Circumstance $circumstance, int $relative_time, int $method = Action::METHOD_DETACHED):Alteration {

		// формируем изменяющее действие
		$this->_fillAlteration($alteration, $relative_time);
		$action   = $this->_fillAction($method, $alteration, $relative_time);
		$expected = $this->_fillDynamic($alteration, $circumstance, $action);

		return $this->_arrangeAlteration($alteration, $circumstance, $action, $expected);
	}

	/**
	 * Возвращает набор опций, удовлетворяющих указанным условиям.
	 *
	 * Логика работы ракова:
	 *    1) Сначала выгружаем данные с необходимыми опциями извне (например из goods_id);
	 *    2) Передаем в тарифный план данные о текущем окружении и дополняем полученный
	 *        ранее массив опций удовлетворяющими опциями из тарифного плана;
	 *    3) Пытаемся применить полный набор опций к тарифному плану;
	 *    4) Сохраняем изменения тарифного плана.
	 */
	public function applyAlteration(int $method, Alteration $alteration, Circumstance $circumstance, int $relative_time):\Tariff\Plan\AlterationResult {

		// формируем изменяющее действие
		$this->_fillAlteration($alteration, $relative_time);
		$action   = $this->_fillAction($method, $alteration, $relative_time);
		$expected = $this->_fillDynamic($alteration, $circumstance, $action);

		// выполняем проверки, если не передано форсированное действие
		if ($action->isForced()) {

			$this->_applyAlteration($expected);
			return new \Tariff\Plan\AlterationResult(0, "forced action");
		}

		$alteration_result = $this->_assertAlteration($alteration, $circumstance, $action, $expected);

		if (!$alteration_result->isSuccess()) {
			return $alteration_result;
		}

		return $this->_applyAlteration($expected);
	}

	/**
	 * Проверяет альтерацию перед применением.
	 */
	protected function _assertAlteration(Alteration $alteration, Circumstance $circumstance, Action $action, Dynamic $expected):\Tariff\Plan\AlterationResult {

		// делаем альтерацию более строгой, логика такова, что формирование
		// возможных альтераций и выполнение альтераций должны проходить через
		// один набор логики, т.е. витрина формируется из позиций, которые точно
		// могут быть активированы на момент создания витрины, проверка при активации
		// задействует ту же самую логику, что позволяет избежать рассинхронизации
		$this->_arrangeAlteration($alteration, $circumstance, $action, $expected);

		foreach ($alteration->_extra_conditions as $extra_condition) {
			$alteration->availability->arrange($extra_condition($this));
		}

		// если альтерацию что-то забраковало
		if (!$alteration->availability->isAvailable()) {
			return new \Tariff\Plan\AlterationResult($alteration->availability->getCode(), $alteration->availability->getMessage());
		}

		// если способ альтерации не имеет основания, но оно требуется
		if ($alteration->availability->isReasonRequired() && !$action->hasReason()) {
			return new \Tariff\Plan\AlterationResult(1, "payment required");
		}

		return new \Tariff\Plan\AlterationResult();
	}

	/**
	 * Делает правила альтерации более строгими.
	 * @long много условий
	 */
	protected function _arrangeAlteration(Alteration $alteration, Circumstance $circumstance, Action $action, Dynamic $expected):Alteration {

		$action_time = $action->getTime();

		// проверяем, что текущее число участников совпадает с ожидаемым
		if (isset($alteration->expected_option_limit) && !$this->_option_limit->isSame($alteration->expected_option_limit)) {
			$alteration->availability->arrange(new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_PLAN_WAS_CHANGED, "member count not equal to expected"));
		}

		// для активного тарифа необходимо проверить
		// что ожидаемый срок действия совпадает с текущим
		if (($this->_active_till < ($action_time - $alteration->active_till_min_bound) || $this->_active_till > ($action_time + $alteration->active_till_max_bound))
			&& $this->isActive($action_time)
			&& ($alteration->isStrictActiveTillRequired() || $alteration->isRangeActiveTillRequired())
		) {
			$alteration->availability->arrange(new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_PLAN_UNEXPECTED_EXPIRATION_DATE, "expiration date not equal to expected"));
		}

		// проверяем, если текущий тариф истек, то для его продления необходим
		// флаг действия — «активация», если его нет, то действие невозможно выполнить
		if (!$alteration->isActivation() && $alteration->isProlongationExtend() && !$this->isActive($action->getTime())) {
			$alteration->availability->arrange(new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, 1, "tariff plan need to be activated with ACTIVATION action first"));
		}

		// если план в данный момент активен, то альтерация
		// активации доступна только при текущем бесплатном доступе
		if ($alteration->isActivation() && $this->isActive($action_time) && !$this->isFree($action_time)) {
			$alteration->availability->arrange(new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, 1, "plan is active"));
		}

		// проходим по всем опциям и повышаем строгость альтерации
		$alteration->availability->arrange(
			$this->_option_limit->resolveAlterationAvailability($circumstance, $action, $this->_last_dynamic, $expected),
			$this->_option_restrict_policy->resolveAlterationAvailability($circumstance, $action, $this->_last_dynamic, $expected),
		);

		return $alteration;
	}

	/**
	 * Применяет новое состояние к тарифу.
	 */
	protected function _applyAlteration(Dynamic $expected_dynamic):\Tariff\Plan\AlterationResult {

		// обновляем опции тарифного плана
		$this->_option_limit           = $expected_dynamic->option_limit;
		$this->_option_restrict_policy = $expected_dynamic->option_restrict_policy;
		$this->_option_demo            = $expected_dynamic->option_demo;

		// обновляем сроки действия тарифного плана
		$this->_active_till      = $expected_dynamic->active_till;
		$this->_free_active_till = $expected_dynamic->free_active_till;

		$this->_last_dynamic = $expected_dynamic;

		return new \Tariff\Plan\AlterationResult();
	}

	/**
	 * Формирует объект изменяющего действия.
	 */
	protected function _fillAlteration(Alteration $alteration, int $relative_time):Alteration {

		// если есть переход на бесплатный лимит
		// то устанавливаем бесконечную длительность для плана
		if (isset($alteration->option_limit) && $alteration->option_limit->isFree()) {

			$alteration->setProlongation(BaseAlteration::PROLONGATION_RULE_INFINITE);
			$alteration->addAction(BaseAlteration::PROLONG);
		}

		return $alteration;
	}

	/**
	 * Формирует объект изменяющего действия.
	 */
	protected function _fillAction(int $method, Alteration $alteration, int $relative_time):Action {

		if ($this->isActive($relative_time)) {

			if ($this->_active_till === 0) {
				$active_state_list[] = Action::ACTIVE_STATE_INFINITE;
			}

			if ($this->_active_till > $relative_time) {
				$active_state_list[] = Action::ACTIVE_STATE_NOT_EXPIRED;
			}

			if ($this->_option_limit->isFree()) {
				$active_state_list[] = Action::ACTIVE_STATE_FREE_LIMIT;
			}
		} else {
			$active_state_list = [Action::ACTIVE_STATE_NONE];
		}

		return new Action($method, $alteration, BaseAction::TRIAL_STATE_NONE, $active_state_list ?? [Action::ACTIVE_STATE_NONE], $relative_time);
	}

	/**
	 * Формирует объект ожидаемых изменений.
	 */
	protected function _fillDynamic(Alteration $alteration, Circumstance $circumstance, Action $action):Dynamic {

		// считаем ожидаемые даты окончания
		[$expected_active_till, $expected_free_active_till] = $this->_calculateActiveDates($alteration, $action);

		$opt_limit           = $alteration->option_limit ?? null;
		$opt_restrict_policy = $alteration->option_restrict_policy ?? null;
		$opt_demo            = $alteration->option_demo ?? null;

		// сначала формируем ожидаемый результат с указанными извне опциями, если такие есть
		$dynamic = new Dynamic($expected_active_till, $expected_free_active_till, $opt_limit, $opt_restrict_policy, $opt_demo);

		// устанавливаем подходящее значение опций, в данный момент
		// не передаем расчетные значения указываем только те, что пришли извне
		$opt_limit           = $opt_limit ?? $this->_option_limit->makeAlterationReplacer($circumstance, $action, $this->_last_dynamic, $dynamic);
		$opt_restrict_policy = $opt_restrict_policy ?? $this->_option_restrict_policy->makeAlterationReplacer($circumstance, $action, $this->_last_dynamic, $dynamic);
		$opt_demo            = $opt_demo ?? $this->_option_demo->makeAlterationReplacer($circumstance, $action, $this->_last_dynamic, $dynamic);

		return new Dynamic($expected_active_till, $expected_free_active_till, $opt_limit, $opt_restrict_policy, $opt_demo);
	}

	/**
	 * Получить время начала ограничения
	 */
	public function getRestrictedAccessFrom():int {

		return $this->_option_restrict_policy->getActiveFrom();
	}

	/**
	 * @inheritDoc
	 */
	public function getDemoActiveTill():int {

		return $this->_option_demo->getActiveTill();
	}

	# endregion методы изменения
	# region методы загрузки

	/**
	 * Создает тарифный план из явных данных.
	 */
	public static function fromData(int $active_till, int $free_active_till, array $option_data_list):static {

		// создаем опции из полученных данных
		$option_limit           = new OptionLimit(...static::_resolveOptionData(static::_LIMIT_OPTION_KEY, $option_data_list));
		$option_restrict_policy = new OptionRestrictPolicy(...static::_resolveOptionData(static::_RESTRICT_POLICY_OPTION_KEY, $option_data_list));
		$option_demo            = new OptionDemo(...static::_resolveOptionData(static::_DEMO_OPTION_KEY, $option_data_list));

		return new static($active_till, $free_active_till, $option_limit, $option_restrict_policy, $option_demo);
	}

	/**
	 * Создает тарифный план из набора записей базы данных.
	 */
	public static function fromRows(array $row_list):static {

		$plan_argument_list                   = ["active_till" => 0, "free_active_till" => 0];
		$option_limit_argument_list           = static::_resolveOptionData(static::_LIMIT_OPTION_KEY, []);
		$option_restrict_policy_argument_list = static::_resolveOptionData(static::_RESTRICT_POLICY_OPTION_KEY, []);
		$option_demo_argument_list            = static::_resolveOptionData(static::_DEMO_OPTION_KEY, []);

		if (count($row_list) === 0) {
			return static::fromData(0, 0, []);
		}

		// получаем список функций, которые
		// сгенерируют нужные данные для конструкторов
		$option_limit_load_fn           = OptionLimit::makeLoadFn($option_limit_argument_list);
		$option_restrict_policy_load_fn = OptionRestrictPolicy::makeLoadFn($option_restrict_policy_argument_list);
		$option_demo_load_fn            = OptionDemo::makeLoadFn($option_demo_argument_list);
		$plan_load_fn                   = static::_makeLoadFn($plan_argument_list);

		// в один проход перебираем все записи
		foreach ($row_list as $row) {

			$plan_argument_list                   = $plan_load_fn($row);
			$option_limit_argument_list           = $option_limit_load_fn($row);
			$option_restrict_policy_argument_list = $option_restrict_policy_load_fn($row);
			$option_demo_argument_list            = $option_demo_load_fn($row);
		}

		$plan_argument_list["option_data_list"][static::_LIMIT_OPTION_KEY]           = $option_limit_argument_list;
		$plan_argument_list["option_data_list"][static::_RESTRICT_POLICY_OPTION_KEY] = $option_restrict_policy_argument_list;
		$plan_argument_list["option_data_list"][static::_DEMO_OPTION_KEY]            = $option_demo_argument_list;

		return static::fromData(...$plan_argument_list);
	}

	# endregion методы загрузки
}