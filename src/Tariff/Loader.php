<?php declare(strict_types=1);

namespace Tariff;

/**
 * Класс для загрузки всех тарифных планов разом.
 *
 * Необходимо унаследовать от него другой класс, и там повысить
 * доступность методов fromRows и fromData или сделать соответствующий singleton.
 */
abstract class Loader {

	// у плана есть два типа ключей — буквенный и числовой
	public const MEMBER_COUNT_PLAN_KEY = "member_count";
	public const MEMBER_COUNT_PLAN_ID  = 1000;

	/** @var Plan\MemberCount\MemberCount[] список планов числа участников */
	protected const _KNOWN_MEMBER_COUNT_PLAN_LIST = [
		\Tariff\Plan\MemberCount\Default\Plan::PLAN_ID => \Tariff\Plan\MemberCount\Default\Plan::class
	];

	// список связи ключ плана — идентификатор плана
	// чтобы не делать два отдельных списка для планов
	protected const _PLAN_ID_KEY_DICTIONARY = [
		self::MEMBER_COUNT_PLAN_ID => self::MEMBER_COUNT_PLAN_KEY,
	];

	// данные для планов по умолчанию
	protected const _DEFAULT_DATA = [

	];

	protected array $_stored_rows      = []; // сгруппированные данные для загрузки из строк
	protected array $_stored_last_rows = []; // сгруппированные данные для загрузки из строк
	protected array $_stored_data      = []; // сгруппированные данные для загрузки из конфигов

	protected ?Plan\MemberCount\MemberCount $_member_count_plan = null;

	/**
	 * Закрываем конструктор.
	 */
	protected function __construct() {

		// ничего не делаем
	}

	/**
	 * Возвращает загруженный тарифный план для участников.
	 */
	public function memberCount():Plan\MemberCount\MemberCount {

		$key = static::MEMBER_COUNT_PLAN_KEY;

		if (is_null($this->_member_count_plan)) {

			if (isset($this->_stored_rows[$key])) {

				// пытаемся загрузить из строк
				$this->_member_count_plan = static::_loadMemberCountFromRows($this->_stored_rows[$key], $this->_stored_last_rows[$key]);
			} elseif (isset($this->_stored_data[$key])) {

				// затем пытаемся загрузить из конкретных данные
				$this->_member_count_plan = static::_loadMemberCountFromData($this->_stored_data[$key]);
			} elseif (isset(static::_DEFAULT_DATA[$key])) {

				// пробуем данные по умолчанию
				$this->_member_count_plan = static::_loadMemberCountFromData(static::_DEFAULT_DATA[$key]);
			} else {

				// если данные не были загружено, то что поделать
				throw new \RuntimeException("member count plan data wasn't load");
			}
		}

		return $this->_member_count_plan;
	}

	/**
	 * Выполняет загрузку тарифных планов из записей базы данных.
	 */
	protected function _loadRows(array $row_list):static {

		foreach ($row_list as $row) {

			$plan_numeric_type = (int) $row["type"];

			if (!isset(static::_PLAN_ID_KEY_DICTIONARY[$plan_numeric_type])) {
				continue;
			}

			$plan_type                        = static::_PLAN_ID_KEY_DICTIONARY[$plan_numeric_type];
			$this->_stored_rows[$plan_type][] = $row;

			// пытаемся определить строку с максимальным значением id
			if (!isset($this->_stored_last_rows[$plan_type]) || (int) $this->_stored_last_rows[$plan_type]["id"] < (int) $row["id"]) {
				$this->_stored_last_rows[$plan_type] = $row;
			}
		}

		// загружаем данные и возвращаем управляющий объект
		return $this;
	}

	/**
	 * Выполняет загрузку тарифных планов из указанных данных..
	 */
	protected function _loadData(array $data_list):static {

		foreach ($data_list as $plan_type => $data) {
			$this->_stored_data[$plan_type] = $data;
		}

		return $this;
	}

	/**
	 * Загружает соответствующий тарифный план ограничения числа участников.
	 */
	protected static function _loadMemberCountFromRows(array $row_list, array $last_row):Plan\MemberCount\MemberCount {

		// без записей создаем план по умолчанию
		if (count($row_list) === 0) {
			throw new \RuntimeException("passed empty row list");
		}

		// из последней записи понимаем, какая это вариация плана
		$plan_id = (int) $last_row["plan_id"];

		// проверяем, что такой план известен
		if (!array_key_exists($plan_id, static::_KNOWN_MEMBER_COUNT_PLAN_LIST)) {
			throw new \RuntimeException("resolved unknown plan $plan_id");
		}

		/** @var Plan\MemberCount\MemberCount $plan_class */
		$plan_class = static::_KNOWN_MEMBER_COUNT_PLAN_LIST[$plan_id];
		return $plan_class::fromRows($row_list);
	}

	/**
	 * Загружает соответствующий тарифный план ограничения числа участников.
	 */
	protected static function _loadMemberCountFromData(array $data):Plan\MemberCount\MemberCount {

		extract($data);

		if (!array_key_exists($plan_id, static::_KNOWN_MEMBER_COUNT_PLAN_LIST)) {
			throw new \RuntimeException("resolved unknown plan $plan_id");
		}

		/** @var Plan\MemberCount\MemberCount $plan_class */
		$plan_class = static::_KNOWN_MEMBER_COUNT_PLAN_LIST[$plan_id];
		return $plan_class::fromData($active_till, $free_active_till, $option_list);
	}
}
