<?php declare(strict_types = 1);

namespace Tariff\Plan\MemberCount;

/**
 * Опция тарифного плана, описывающее поведение
 * для ограничения числа участников пространства.
 */
#[\JetBrains\PhpStorm\Immutable]
class OptionLimit extends Option {

	public const OPTION_NAME = "limit";

	// допустимые значения опции
	public const LIMIT_10   = 10;
	public const LIMIT_15   = 15;
	public const LIMIT_20   = 20;
	public const LIMIT_25   = 25;
	public const LIMIT_30   = 30;
	public const LIMIT_35   = 35;
	public const LIMIT_40   = 40;
	public const LIMIT_45   = 45;
	public const LIMIT_50   = 50;
	public const LIMIT_55   = 55;
	public const LIMIT_60   = 60;
	public const LIMIT_65   = 65;
	public const LIMIT_70   = 70;
	public const LIMIT_75   = 75;
	public const LIMIT_80   = 80;
	public const LIMIT_85   = 85;
	public const LIMIT_90   = 90;
	public const LIMIT_95   = 95;
	public const LIMIT_100  = 100;
	public const LIMIT_110  = 110;
	public const LIMIT_120  = 120;
	public const LIMIT_130  = 130;
	public const LIMIT_140  = 140;
	public const LIMIT_150  = 150;
	public const LIMIT_160  = 160;
	public const LIMIT_170  = 170;
	public const LIMIT_180  = 180;
	public const LIMIT_190  = 190;
	public const LIMIT_200  = 200;
	public const LIMIT_210  = 210;
	public const LIMIT_220  = 220;
	public const LIMIT_230  = 230;
	public const LIMIT_240  = 240;
	public const LIMIT_250  = 250;
	public const LIMIT_260  = 260;
	public const LIMIT_270  = 270;
	public const LIMIT_280  = 280;
	public const LIMIT_290  = 290;
	public const LIMIT_300  = 300;
	public const LIMIT_310  = 310;
	public const LIMIT_320  = 320;
	public const LIMIT_330  = 330;
	public const LIMIT_340  = 340;
	public const LIMIT_350  = 350;
	public const LIMIT_360  = 360;
	public const LIMIT_370  = 370;
	public const LIMIT_380  = 380;
	public const LIMIT_390  = 390;
	public const LIMIT_400  = 400;
	public const LIMIT_410  = 410;
	public const LIMIT_420  = 420;
	public const LIMIT_430  = 430;
	public const LIMIT_440  = 440;
	public const LIMIT_450  = 450;
	public const LIMIT_460  = 460;
	public const LIMIT_470  = 470;
	public const LIMIT_480  = 480;
	public const LIMIT_490  = 490;
	public const LIMIT_500  = 500;
	public const LIMIT_510  = 510;
	public const LIMIT_520  = 520;
	public const LIMIT_530  = 530;
	public const LIMIT_540  = 540;
	public const LIMIT_550  = 550;
	public const LIMIT_560  = 560;
	public const LIMIT_570  = 570;
	public const LIMIT_580  = 580;
	public const LIMIT_590  = 590;
	public const LIMIT_600  = 600;
	public const LIMIT_610  = 610;
	public const LIMIT_620  = 620;
	public const LIMIT_630  = 630;
	public const LIMIT_640  = 640;
	public const LIMIT_650  = 650;
	public const LIMIT_660  = 660;
	public const LIMIT_670  = 670;
	public const LIMIT_680  = 680;
	public const LIMIT_690  = 690;
	public const LIMIT_700  = 700;
	public const LIMIT_710  = 710;
	public const LIMIT_720  = 720;
	public const LIMIT_730  = 730;
	public const LIMIT_740  = 740;
	public const LIMIT_750  = 750;
	public const LIMIT_760  = 760;
	public const LIMIT_770  = 770;
	public const LIMIT_780  = 780;
	public const LIMIT_790  = 790;
	public const LIMIT_800  = 800;
	public const LIMIT_810  = 810;
	public const LIMIT_820  = 820;
	public const LIMIT_830  = 830;
	public const LIMIT_840  = 840;
	public const LIMIT_850  = 850;
	public const LIMIT_860  = 860;
	public const LIMIT_870  = 870;
	public const LIMIT_880  = 880;
	public const LIMIT_890  = 890;
	public const LIMIT_900  = 900;
	public const LIMIT_910  = 910;
	public const LIMIT_920  = 920;
	public const LIMIT_930  = 930;
	public const LIMIT_940  = 940;
	public const LIMIT_950  = 950;
	public const LIMIT_960  = 960;
	public const LIMIT_970  = 970;
	public const LIMIT_980  = 980;
	public const LIMIT_990  = 990;
	public const LIMIT_1000 = 1000;

	/** @var array список значений, доступных бесплатно */
	protected const _FREE_LIMIT_LIST = [self::LIMIT_10];

	public const ERROR_EXCEEDED                     = 100_00_2_10; // допустимый лимит опции превышен
	public const ERROR_REASON_REQUIRED              = 100_00_2_20; // изменение опции требует основания
	public const ERROR_CHANGE_WITHOUT_CHANGE_ACTION = 100_00_2_30; // изменение тарифа невозможно без соответствующего флага действия
	public const ERROR_UNSUPPORTED_REPLACEMENT      = 100_00_2_90; // что-от неизвестное происходит

	/** @var string[] допустимые значения числа участников */
	public const ALLOWED_VALUE_LIST = [
		self::LIMIT_10,
		self::LIMIT_15,
		self::LIMIT_20,
		self::LIMIT_25,
		self::LIMIT_30,
		self::LIMIT_35,
		self::LIMIT_40,
		self::LIMIT_45,
		self::LIMIT_50,
		self::LIMIT_55,
		self::LIMIT_60,
		self::LIMIT_65,
		self::LIMIT_70,
		self::LIMIT_75,
		self::LIMIT_80,
		self::LIMIT_85,
		self::LIMIT_90,
		self::LIMIT_95,
		self::LIMIT_100,
		self::LIMIT_110,
		self::LIMIT_120,
		self::LIMIT_130,
		self::LIMIT_140,
		self::LIMIT_150,
		self::LIMIT_160,
		self::LIMIT_170,
		self::LIMIT_180,
		self::LIMIT_190,
		self::LIMIT_200,
		self::LIMIT_210,
		self::LIMIT_220,
		self::LIMIT_230,
		self::LIMIT_240,
		self::LIMIT_250,
		self::LIMIT_260,
		self::LIMIT_270,
		self::LIMIT_280,
		self::LIMIT_290,
		self::LIMIT_300,
		self::LIMIT_310,
		self::LIMIT_320,
		self::LIMIT_330,
		self::LIMIT_340,
		self::LIMIT_350,
		self::LIMIT_360,
		self::LIMIT_370,
		self::LIMIT_380,
		self::LIMIT_390,
		self::LIMIT_400,
		self::LIMIT_410,
		self::LIMIT_420,
		self::LIMIT_430,
		self::LIMIT_440,
		self::LIMIT_450,
		self::LIMIT_460,
		self::LIMIT_470,
		self::LIMIT_480,
		self::LIMIT_490,
		self::LIMIT_500,
		self::LIMIT_510,
		self::LIMIT_520,
		self::LIMIT_530,
		self::LIMIT_540,
		self::LIMIT_550,
		self::LIMIT_560,
		self::LIMIT_570,
		self::LIMIT_580,
		self::LIMIT_590,
		self::LIMIT_600,
		self::LIMIT_610,
		self::LIMIT_620,
		self::LIMIT_630,
		self::LIMIT_640,
		self::LIMIT_650,
		self::LIMIT_660,
		self::LIMIT_670,
		self::LIMIT_680,
		self::LIMIT_690,
		self::LIMIT_700,
		self::LIMIT_710,
		self::LIMIT_720,
		self::LIMIT_730,
		self::LIMIT_740,
		self::LIMIT_750,
		self::LIMIT_760,
		self::LIMIT_770,
		self::LIMIT_780,
		self::LIMIT_790,
		self::LIMIT_800,
		self::LIMIT_810,
		self::LIMIT_820,
		self::LIMIT_830,
		self::LIMIT_840,
		self::LIMIT_850,
		self::LIMIT_860,
		self::LIMIT_870,
		self::LIMIT_880,
		self::LIMIT_890,
		self::LIMIT_900,
		self::LIMIT_910,
		self::LIMIT_920,
		self::LIMIT_930,
		self::LIMIT_940,
		self::LIMIT_950,
		self::LIMIT_960,
		self::LIMIT_970,
		self::LIMIT_980,
		self::LIMIT_990,
		self::LIMIT_1000,
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

			if ($limit < $circumstance->current_member_count) {
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

		// новая опция не удовлетворяет указанным параметрам окружения
		if (!$expected_result->option_limit->isFit($circumstance)) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_EXCEEDED, "not fit");
		}

		// для бесконечного тарифного плана можно использовать только разрешенные значения
		if ($expected_result->active_till === 0 && in_array($this->_value, static::_FREE_LIMIT_LIST, true)) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_UNSUPPORTED_REPLACEMENT, "can not be infinite");
		}

		// пока для активации доступен пробный период
		// все действия, кроме активации запрещены
		if ($action->isTrialAvailable() && !$action->alteration->isActivation()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_UNSUPPORTED_REPLACEMENT, "need activate or start before");
		}

		// если план не активен и это не активация, то дейсвтие запрещено
		if (!$action->isActiveOnInfinite() && !$action->isActiveOnNotExpired() && !$action->alteration->isActivation() && !$expected_result->option_limit->isFree()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_UNSUPPORTED_REPLACEMENT, "need activate before");
		}

		// запрещаем уменьшать число пользователей платежом
		if ($this->_value > $expected_result->option_limit->_value && $action->hasReason() && !$action->alteration->isActivation()) {
			return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::UNAVAILABLE_UNAPPROPRIATED, static::ERROR_UNSUPPORTED_REPLACEMENT, "can not decrease with reason");
		}

		// уменьшать всегда позволяем бесплатно,
		// во время пробного периода нельзя делать бесплатные продления
		if (!$action->alteration->isProlongation() && ($this->_value > $expected_result->option_limit->_value || $action->isTrialActive())) {
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

		return $this->_value >= $circumstance->current_member_count;
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
