<?php declare(strict_types=1);

namespace Tariff\Plan;

#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
abstract class BasePlan {

	/** @var array настройки опций по умолчанию */
	protected const _DEFAULT_OPTION_DATA = [];

	/** @var int дата окончания действия */
	protected int $_active_till = 0;

	/**
	 * Возвращает состояние активности плана.
	 * Если у плана длительность равна нулю, то он считается бессрочным.
	 */
	public function isActive(int $relative_date):bool {

		return $this->_active_till === 0 || $this->_active_till > $relative_date;
	}

	/**
	 * Возвращает дату действия тарифа.
	 */
	public function getActiveTill():int {

		return $this->_active_till;
	}

	/**
	 * Проверяет наличие изменений с исходными данными.
	 */
	public function hasChanges():bool {

		return $this->_active_till !== $this->_original_dynamic->active_till
			|| $this->_free_active_till !== $this->_original_dynamic->free_active_till
			|| $this->_hasOptionChanges();
	}

	/**
	 * Проверяет наличие изменений опций.
	 */
	protected function _hasOptionChanges():bool {

		return false;
	}

	/**
	 * Возвращает данные для сохранения.
	 */
	abstract public function getData():BaseSaveData;

	/**
	 * Возвращает массив данных, из которых
	 * можно полностью восстановить тарифный план.
	 */
	#[\JetBrains\PhpStorm\ArrayShape(["type" => "int", "valid_till" => "int", "plan_id" => "int", "active_till" => "int", "free_active_till" => "int", "option_list" => "array"])]
	protected function _getSaveData():array {

		return [
			"plan_id"          => static::PLAN_ID,
			"valid_till"       => 2147483647,
			"active_till"      => $this->_active_till,
			"free_active_till" => $this->_free_active_till,
			"option_list"      => $this->_getOptionSaveData(),
		];
	}

	/**
	 * Возвращает массив для сохранения данных опций.
	 */
	protected function _getOptionSaveData():array {

		return [];
	}

	/**
	 * Возвращает даты для продления действия тарифного плана.
	 */
	protected function _calculateActiveDates(BaseAlteration $alteration, BaseAction $action):array {

		// бесконечный тарифный план
		if ($alteration->isProlongationInfinite()) {
			return [0, 0];
		}

		// установить точную дату окончания
		if ($alteration->isProlongationSet()) {

			$updated_free_active_till = $action->isPromo() ? $alteration->prolongation_value : $this->_free_active_till;
			return [$alteration->prolongation_value, $updated_free_active_till];
		}

		// расчетная дата окончания
		if ($alteration->isProlongationExtend()) {

			$updated_active_till      = max($action->getTime(), $this->_active_till) + $alteration->prolongation_value;
			$updated_free_active_till = $action->isPromo() ? max($action->getTime(), $this->_free_active_till) + $alteration->prolongation_value : $this->_free_active_till;

			return [$updated_active_till, $updated_free_active_till];
		}

		// иначе считаем, что это вообще не продление
		return [$this->_active_till, $this->_free_active_till];
	}

	/**
	 * Возвращает данные для создания опции.
	 * Предпочитает брать данные извне, но если их нет, то берет данные по умолчанию.
	 */
	protected static function _resolveOptionData(string $option_key, array $option_data_set = []):array {

		return isset($option_data_set[$option_key])
			? array_merge(static::_DEFAULT_OPTION_DATA[$option_key], $option_data_set[$option_key])
			: static::_DEFAULT_OPTION_DATA[$option_key];
	}

	/**
	 * Возвращает функцию-загрузчик, для генерации
	 * актуальных параметров конструктора тарифного плана.
	 *
	 * На вход получает набор данных по-умолчанию,
	 * который будет использоваться, если походящих данных
	 * в загруженных записях не найдется.
	 *
	 * Должна возвращать функцию, принимающую на вход строку из базы
	 * и возвращающую массив с данными конструктора плана.
	 */
	protected static function _makeLoadFn(array $data):callable {

		$carry  = 0;
		$output = $data;

		return static function(array $row) use (&$carry, &$output) {

			$passed_id = (int) $row["id"];

			if ($passed_id > $carry) {

				$carry  = $passed_id;
				$output = [
					"active_till"      => (int) $row["active_till"],
					"free_active_till" => (int) $row["free_active_till"]
				];
			}

			return $output;
		};
	}
}