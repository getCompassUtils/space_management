<?php declare(strict_types=1);

namespace Tariff\Plan\PremiseUserCount;

/**
 * Опция тарифного плана, описывающее поведение
 * для ограничения числа участников пространства.
 */
#[\JetBrains\PhpStorm\Immutable]
class OptionLimit extends Option {

	public const OPTION_NAME = "limit";

	// допустимые значения опции
	public const LIMIT_10    = 10;
	public const LIMIT_20    = 20;
	public const LIMIT_30    = 30;
	public const LIMIT_40    = 40;
	public const LIMIT_50    = 50;
	public const LIMIT_60    = 60;
	public const LIMIT_70    = 70;
	public const LIMIT_80    = 80;
	public const LIMIT_90    = 90;
	public const LIMIT_100   = 100;
	public const LIMIT_120   = 120;
	public const LIMIT_140   = 140;
	public const LIMIT_160   = 160;
	public const LIMIT_180   = 180;
	public const LIMIT_200   = 200;
	public const LIMIT_220   = 220;
	public const LIMIT_240   = 240;
	public const LIMIT_260   = 260;
	public const LIMIT_280   = 280;
	public const LIMIT_300   = 300;
	public const LIMIT_320   = 320;
	public const LIMIT_340   = 340;
	public const LIMIT_360   = 360;
	public const LIMIT_380   = 380;
	public const LIMIT_400   = 400;
	public const LIMIT_420   = 420;
	public const LIMIT_440   = 440;
	public const LIMIT_460   = 460;
	public const LIMIT_480   = 480;
	public const LIMIT_500   = 500;
	public const LIMIT_550   = 550;
	public const LIMIT_600   = 600;
	public const LIMIT_650   = 650;
	public const LIMIT_700   = 700;
	public const LIMIT_750   = 750;
	public const LIMIT_800   = 800;
	public const LIMIT_850   = 850;
	public const LIMIT_900   = 900;
	public const LIMIT_950   = 950;
	public const LIMIT_1000  = 1000;
	public const LIMIT_1100  = 1100;
	public const LIMIT_1200  = 1200;
	public const LIMIT_1300  = 1300;
	public const LIMIT_1400  = 1400;
	public const LIMIT_1500  = 1500;
	public const LIMIT_1600  = 1600;
	public const LIMIT_1700  = 1700;
	public const LIMIT_1800  = 1800;
	public const LIMIT_1900  = 1900;
	public const LIMIT_2000  = 2000;
	public const LIMIT_2200  = 2200;
	public const LIMIT_2400  = 2400;
	public const LIMIT_2600  = 2600;
	public const LIMIT_2800  = 2800;
	public const LIMIT_3000  = 3000;
	public const LIMIT_3200  = 3200;
	public const LIMIT_3400  = 3400;
	public const LIMIT_3600  = 3600;
	public const LIMIT_3800  = 3800;
	public const LIMIT_4000  = 4000;
	public const LIMIT_4200  = 4200;
	public const LIMIT_4400  = 4400;
	public const LIMIT_4600  = 4600;
	public const LIMIT_4800  = 4800;
	public const LIMIT_5000  = 5000;
	public const LIMIT_5500  = 5500;
	public const LIMIT_6000  = 6000;
	public const LIMIT_6500  = 6500;
	public const LIMIT_7000  = 7000;
	public const LIMIT_7500  = 7500;
	public const LIMIT_8000  = 8000;
	public const LIMIT_8500  = 8500;
	public const LIMIT_9000  = 9000;
	public const LIMIT_9500  = 9500;
	public const LIMIT_10000 = 10000;
	public const LIMIT_10500 = 10500;
	public const LIMIT_11000 = 11000;
	public const LIMIT_11500 = 11500;
	public const LIMIT_12000 = 12000;
	public const LIMIT_12500 = 12500;
	public const LIMIT_13000 = 13000;
	public const LIMIT_13500 = 13500;
	public const LIMIT_14000 = 14000;
	public const LIMIT_14500 = 14500;
	public const LIMIT_15000 = 15000;
	public const LIMIT_15500 = 15500;
	public const LIMIT_16000 = 16000;
	public const LIMIT_16500 = 16500;
	public const LIMIT_17000 = 17000;
	public const LIMIT_17500 = 17500;
	public const LIMIT_18000 = 18000;
	public const LIMIT_18500 = 18500;
	public const LIMIT_19000 = 19000;
	public const LIMIT_19500 = 19500;
	public const LIMIT_20000 = 20000;
	public const LIMIT_20500 = 20500;
	public const LIMIT_21000 = 21000;
	public const LIMIT_21500 = 21500;
	public const LIMIT_22000 = 22000;
	public const LIMIT_22500 = 22500;
	public const LIMIT_23000 = 23000;
	public const LIMIT_23500 = 23500;
	public const LIMIT_24000 = 24000;
	public const LIMIT_24500 = 24500;
	public const LIMIT_25000 = 25000;
	public const LIMIT_25500 = 25500;
	public const LIMIT_26000 = 26000;
	public const LIMIT_26500 = 26500;
	public const LIMIT_27000 = 27000;
	public const LIMIT_27500 = 27500;
	public const LIMIT_28000 = 28000;
	public const LIMIT_28500 = 28500;
	public const LIMIT_29000 = 29000;
	public const LIMIT_29500 = 29500;
	public const LIMIT_30000 = 30000;

	/** @var array список значений, доступных бесплатно */
	protected const _FREE_LIMIT_LIST = [self::LIMIT_10];

	public const ERROR_EXCEEDED                     = 200_00_2_10; // допустимый лимит опции превышен
	public const ERROR_REASON_REQUIRED              = 200_00_2_20; // изменение опции требует основания
	public const ERROR_CHANGE_WITHOUT_CHANGE_ACTION = 200_00_2_30; // изменение тарифа невозможно без соответствующего флага действия
	public const ERROR_UNSUPPORTED_REPLACEMENT      = 200_00_2_90; // что-от неизвестное происходит

	/** @var string[] допустимые значения числа участников */
	public const ALLOWED_VALUE_LIST = [
		self::LIMIT_10,
		self::LIMIT_20,
		self::LIMIT_30,
		self::LIMIT_40,
		self::LIMIT_50,
		self::LIMIT_60,
		self::LIMIT_70,
		self::LIMIT_80,
		self::LIMIT_90,
		self::LIMIT_100,
		self::LIMIT_120,
		self::LIMIT_140,
		self::LIMIT_160,
		self::LIMIT_180,
		self::LIMIT_200,
		self::LIMIT_220,
		self::LIMIT_240,
		self::LIMIT_260,
		self::LIMIT_280,
		self::LIMIT_300,
		self::LIMIT_320,
		self::LIMIT_340,
		self::LIMIT_360,
		self::LIMIT_380,
		self::LIMIT_400,
		self::LIMIT_420,
		self::LIMIT_440,
		self::LIMIT_460,
		self::LIMIT_480,
		self::LIMIT_500,
		self::LIMIT_550,
		self::LIMIT_600,
		self::LIMIT_650,
		self::LIMIT_700,
		self::LIMIT_750,
		self::LIMIT_800,
		self::LIMIT_850,
		self::LIMIT_900,
		self::LIMIT_950,
		self::LIMIT_1000,
		self::LIMIT_1100,
		self::LIMIT_1200,
		self::LIMIT_1300,
		self::LIMIT_1400,
		self::LIMIT_1500,
		self::LIMIT_1600,
		self::LIMIT_1700,
		self::LIMIT_1800,
		self::LIMIT_1900,
		self::LIMIT_2000,
		self::LIMIT_2200,
		self::LIMIT_2400,
		self::LIMIT_2600,
		self::LIMIT_2800,
		self::LIMIT_3000,
		self::LIMIT_3200,
		self::LIMIT_3400,
		self::LIMIT_3600,
		self::LIMIT_3800,
		self::LIMIT_4000,
		self::LIMIT_4200,
		self::LIMIT_4400,
		self::LIMIT_4600,
		self::LIMIT_4800,
		self::LIMIT_5000,
		self::LIMIT_5500,
		self::LIMIT_6000,
		self::LIMIT_6500,
		self::LIMIT_7000,
		self::LIMIT_7500,
		self::LIMIT_8000,
		self::LIMIT_8500,
		self::LIMIT_9000,
		self::LIMIT_9500,
		self::LIMIT_10000,
		self::LIMIT_10500,
		self::LIMIT_11000,
		self::LIMIT_11500,
		self::LIMIT_12000,
		self::LIMIT_12500,
		self::LIMIT_13000,
		self::LIMIT_13500,
		self::LIMIT_14000,
		self::LIMIT_14500,
		self::LIMIT_15000,
		self::LIMIT_15500,
		self::LIMIT_16000,
		self::LIMIT_16500,
		self::LIMIT_17000,
		self::LIMIT_17500,
		self::LIMIT_18000,
		self::LIMIT_18500,
		self::LIMIT_19000,
		self::LIMIT_19500,
		self::LIMIT_20000,
		self::LIMIT_20500,
		self::LIMIT_21000,
		self::LIMIT_21500,
		self::LIMIT_22000,
		self::LIMIT_22500,
		self::LIMIT_23000,
		self::LIMIT_23500,
		self::LIMIT_24000,
		self::LIMIT_24500,
		self::LIMIT_25000,
		self::LIMIT_25500,
		self::LIMIT_26000,
		self::LIMIT_26500,
		self::LIMIT_27000,
		self::LIMIT_27500,
		self::LIMIT_28000,
		self::LIMIT_28500,
		self::LIMIT_29000,
		self::LIMIT_29500,
		self::LIMIT_30000,
	];

	/** @var int текущее значение числа участников */
	protected int $_value;

	/**
	 * Конструктор.
	 */
	public function __construct(int $value) {

		// проверяем входные политики
		$this::_assertValue($value);
		$this->_value = $value;
	}

	/**
	 * Проверяет указанную политику расширения.
	 */
	protected static function _assertValue(int $value):void {

		if (!in_array($value, static::ALLOWED_VALUE_LIST, true)) {
			throw new \InvalidArgumentException("passed bad limit value $value");
		}
	}

	/**
	 * Возвращает текущее ограничение.
	 */
	public function getValue():int {

		return $this->_value;
	}

	/**
	 * Возвращает увеличенное значение для опции,
	 * удовлетворяющее необходимому числу участников.
	 *
	 * Если подходящего варианта опции нет, возвращает false.
	 */
	public function makeAlterationReplacer(Circumstance $circumstance, Action $action, Dynamic $current_state, Dynamic $expected_state):static {

		// если текущее число удовлетворяет требованию,
		// то просто возвращает текущий объект
		if ($this->isFit($circumstance)) {
			return $this;
		}

		foreach (static::ALLOWED_VALUE_LIST as $limit) {

			if ($limit < $circumstance->current_user_count) {
				continue;
			}

			return new static($limit);
		}

		// в противном случае возвращаем максимальный лимит
		return new static(max(static::ALLOWED_VALUE_LIST));
	}

	/**
	 * @inheritDoc
	 * @long
	 */
	public function resolveAlterationAvailability(Circumstance $circumstance, Action $action, Dynamic $current_state, Dynamic $expected_result):\Tariff\Plan\AlterationAvailability {

		// если опции совпадают, то сообщаем об этом
		if ($this->isSame($expected_result->option_limit) && $action->alteration->isChange() && !$action->alteration->isActivation()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_SAME, static::ERROR_UNSUPPORTED_REPLACEMENT, "member count is same");
		}

		// если опция меняется, то действие должно быть меняющим, это скорее бизнес-правило,
		// чтобы активный тариф нельзя было продлить и поменять одновременно
		if (!$this->isSame($expected_result->option_limit) && !$action->alteration->isChange()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_CHANGE_WITHOUT_CHANGE_ACTION, "change action required");
		}

		// если достигнут лимит пользователей, запрещаем изменение для всех случаем
		// кроме попытки продлить лицензию без активации (т.е. просто продлить при превышении лимита можно)
		if (!$expected_result->option_limit->isFit($circumstance) && (!$action->alteration->isProlongation() || $action->alteration->isActivation())) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_EXCEEDED, "not fit");
		}

		// для бесконечного тарифного плана можно использовать только разрешенные значения
		if ($expected_result->active_till === 0 && in_array($this->_value, static::_FREE_LIMIT_LIST, true)) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_UNSUPPORTED_REPLACEMENT, "can not be infinite");
		}

		// если план не активен и это не активация, то дейсвтие запрещено
		if (!$action->isActiveOnInfinite() && !$action->isActiveOnNotExpired() && !$action->alteration->isActivation() && !$expected_result->option_limit->isFree()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_UNSUPPORTED_REPLACEMENT, "need activate before");
		}

		// запрещаем уменьшать число пользователей платежом
		if ($this->_value > $expected_result->option_limit->_value && $action->hasReason() && !$action->alteration->isActivation()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_UNSUPPORTED_REPLACEMENT, "can not decrease with reason");
		}

		// если изменяем количество людей, то проверяем, что не можем уменьшить больше, чем есть сейчас пользователей
		if ($action->alteration->isChange() && !$expected_result->option_limit->isFit($circumstance)) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_EXCEEDED, "not fit");
		}

		// уменьшать всегда позволяем бесплатно,
		// во время пробного периода нельзя делать бесплатные продления
		if (!$action->alteration->isProlongation() && ($this->_value > $expected_result->option_limit->_value)) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::AVAILABLE_FREE);
		}

		// если целевой бесплатный, то делаем бесплатным
		if ($expected_result->option_limit->isFree() && !$action->alteration->isProlongationExtend() && !$action->alteration->isProlongationSet()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::AVAILABLE_FREE);
		}

		// по дефолту считаем, что требуется основание для активации
		return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::AVAILABLE_REASON_REQUIRED);
	}

	/**
	 * Является ли значение опции исходным.
	 * Вызывается из плана при необходимости проверить изменения.
	 */
	public function isSame(Option $to_assert):bool {

		return ($to_assert instanceof $this) && $to_assert->_value === $this->_value;
	}

	/**
	 * Проверяет, удовлетворяет ли указанное
	 * состояние системы ограничениям опции.
	 */
	public function isFit(Circumstance $circumstance):bool {

		return $this->_value >= $circumstance->current_user_count;
	}

	/**
	 * Проверяем, является ли тариф в данный момент бесплатным.
	 */
	public function isFree():bool {

		return in_array($this->_value, static::_FREE_LIMIT_LIST, true);
	}

	/**
	 * Экспортирует данные для сохранения.
	 */
	#[\JetBrains\PhpStorm\ArrayShape(["value" => "int"])]
	public function export():array {

		return ["value" => $this->_value];
	}
}
