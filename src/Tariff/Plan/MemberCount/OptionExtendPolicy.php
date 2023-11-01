<?php declare(strict_types = 1);

namespace Tariff\Plan\MemberCount;

use Tariff\Plan\AlterationAvailability;

/**
 * Опция, регулирующая политику расширения плана.
 */
#[\JetBrains\PhpStorm\Immutable]
class OptionExtendPolicy extends Option {

	protected const _FREE_MEMBER_COUNT_THRESHOLD = OptionLimit::LIMIT_10;

	public const OPTION_NAME = "extend_policy";

	public const ERROR_REASON_NOT_ALLOWED      = 100_00_1_10; // соответствующий переход невозможен
	public const ERROR_UNSUPPORTED_REPLACEMENT = 100_00_1_91; // изменение тарифа невозможно без соответствующего флага действия

	public const FREE  = "free";  // политика расширения для бесплатного тарифа
	public const NEVER = "never"; // политика не допускает расширения
	public const TRIAL = "trial"; // политика пробного периода

	/** @var string[] допустимые политики расширения */
	protected const _ALLOWED_EXTEND_POLICY = [
		self::FREE,
		self::NEVER,
		self::TRIAL,
	];

	/** @var string[] значения правил для возвращения */
	protected const _GET_RULE_SCHEMA = [
		self::FREE  => self::FREE,
		self::NEVER => self::NEVER,
		self::TRIAL => self::TRIAL,
	];

	protected string $_rule;              // текущая политика расширения
	protected int    $_trial_active_till; // текущая политика расширения

	/**
	 * Конструктор.
	 */
	public function __construct(string $rule, int $trial_active_till = PHP_INT_MAX) {

		// проверяем входные политики
		$this::_assertExtendPolicy($rule);
		$this->_rule              = $rule;
		$this->_trial_active_till = $trial_active_till;
	}

	/**
	 * @inheritDoc
	 */
	public function makeAlterationReplacer(Circumstance $circumstance, Action $action, Dynamic $current_state, Dynamic $expected_state):static {

		// если это оплата или промо, то автоматически переводим
		// политику расширения в never, без лишних проверок
		// здесь возможен рассинхрон между витриной и активацией
		// поскольку в момент наполнения витрины способ активации
		// нам неизвестен, но в худшем случае для витрины будет
		// вычислено FREE, а при активации NEVER, что не ломает переход
		if ($action->hasReason()) {
			return $this->_rule !== $this::NEVER ? new static(static::NEVER) : $this;
		}

		// пробный период закончился, эта штука позволяет избежать
		// бесплатного расширения при текущем бесплатно плана
		// поскольку ориентир там идет на active_till, который
		// и в бесплатном, и в дефолтном (еще авторасширение возможно) равен 0
		if ($this->_rule === static::TRIAL && $this->_trial_active_till < $action->getTime()) {
			new static(static::NEVER);
		}

		// если сейчас у нас стоит дефолтный тариф, то при необходимости
		// добавить участников свыше бесплатного ограничителя, нужно активировать пробный период
		if ($this->_rule === static::FREE && $circumstance->current_member_count > static::_FREE_MEMBER_COUNT_THRESHOLD) {
			return new static(static::TRIAL);
		}

		// в остальных случаях считаем, что опция не должна измениться
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function resolveAlterationAvailability(Circumstance $circumstance, Action $action, Dynamic $current_state, Dynamic $expected_result):AlterationAvailability {

		// в общем виде эта проверка всегда будет возвращать AVAILABLE_DETACHED,
		// поскольку извне ее задать нельзя напрямую, а замена всегда генерируется подходящим образом;
		// здесь эта проверки скорее как защита от дурака существуют

		if ($this->_rule === static::NEVER && $expected_result->option_extend_policy->_rule !== static::NEVER) {
			return new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_REASON_NOT_ALLOWED, "can not change never politic");
		}

		// переход на стартовую политику невозможен
		if ($this->_rule !== static::FREE && $expected_result->option_extend_policy->_rule === static::FREE) {
			return new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_REASON_NOT_ALLOWED, "free extend policy is not available");
		}

		// переход на пробный возможно только с FREE
		if ($expected_result->option_extend_policy->_rule === static::TRIAL && $this->_rule !== static::FREE && $this->_rule !== static::TRIAL) {
			return new AlterationAvailability(AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_REASON_NOT_ALLOWED, "TRIAL available only after FREE");
		}

		// в противном случае разрешаем
		return new AlterationAvailability(AlterationAvailability::AVAILABLE_DETACHED);
	}

	/**
	 * Возвращает, соответствует ли текущее состояние опции пробному периоду.
	 *
	 * Значение FREE не соответствует, поскольку в таком случае запуск
	 * пробного периода еще не проводился.
	 */
	public function isTrial(int $time):bool {

		return $this->_rule === static::TRIAL;
	}

	/**
	 * Доступность пробного периода.
	 */
	public function isTrialAvailable():bool {

		return $this->_rule === static::FREE;
	}

	/**
	 * @inheritDoc
	 */
	public function isSame(Option $to_assert):bool {

		return ($to_assert instanceof $this) && $to_assert->_rule === $this->_rule;
	}

	/**
	 * Возвращает правило
	 */
	public function getRule():string {

		return self::_GET_RULE_SCHEMA[$this->_rule];
	}

	/**
	 * @inheritDoc
	 */
	#[\JetBrains\PhpStorm\ArrayShape(["rule" => "string"])]
	public function export():array {

		return ["rule" => $this->_rule];
	}

	/**
	 * Проверяет указанную политику расширения.
	 */
	protected static function _assertExtendPolicy(string $extend_policy):void {

		if (!in_array($extend_policy, static::_ALLOWED_EXTEND_POLICY, true)) {
			throw new \InvalidArgumentException("passed bad extend policy $extend_policy");
		}
	}

	/**
	 * @inheritDoc
	 * @long
	 */
	public static function makeLoadFn(array $data):callable {

		static::_assertExtendPolicy($data["rule"]);

		$output = $data;
		$carry  = $output["rule"];

		$output["trial_active_till"] = PHP_INT_MAX;

		// пока в политике только одно поле, поэтому тут можно не делать
		// логику сливания значения с последней известной записью тарифа
		return static function(array $row) use (&$carry, &$output) {

			if (!isset($row["option_list"][static::OPTION_NAME])) {
				return $output;
			}

			// запишем в переменную значение, которое будет проверять, для удобства
			$passed_extend_policy = $row["option_list"][static::OPTION_NAME]["rule"];
			$passed_active_till   = (int) $row["active_till"];

			// проверим триал и запомним его дату действия
			if ($passed_extend_policy === static::TRIAL && $passed_active_till < $output["trial_active_till"]) {
				$output["trial_active_till"] = $passed_active_till;
			}

			// если уже NEVER, то дальше уже нет смысла проверять
			if ($carry === static::NEVER) {
				return $output;
			}

			// нашли NEVER, это конечный статус
			if ($passed_extend_policy === static::NEVER) {

				$carry  = $passed_extend_policy;
				$output = array_merge($output, $row["option_list"][static::OPTION_NAME]);
			}

			// если нашли переход в пробный период и текущее значение по умолчанию
			// то считаем, что сейчас активен пробный период
			if ($passed_extend_policy === static::TRIAL || $carry === static::FREE) {

				$carry  = $passed_extend_policy;
				$output = array_merge($output, $row["option_list"][static::OPTION_NAME]);
			}

			return $output;
		};
	}
}
