<?php declare(strict_types=1);

namespace Tariff\Plan\MemberCount;

/**
 * Опция, регулирующая политику расширения плана.
 */
abstract class Option {

	protected const OPTION_NAME = "";

	/**
	 * Возвращает подходящую для указанного состояния вариацию опции.
	 * Тут всегда нужно что-то возвращать, затем проверка будет в assert вызовах.
	 */
	public function makeAlterationReplacer(Circumstance $circumstance, Action $action, Dynamic $current_state, Dynamic $expected_state):static {

		return $this;
	}

	/**
	 * Выводит способ активации для ActivationItem.
	 */
	public function resolveAlterationAvailability(Circumstance $circumstance, Action $action, Dynamic $current_state, Dynamic $expected_result):\Tariff\Plan\AlterationAvailability {

		return new \Tariff\Plan\AlterationAvailability(\Tariff\Plan\AlterationAvailability::AVAILABLE_DETACHED);
	}

	/**
	 * Проверяет, удовлетворяет ли опция указанному состоянию.
	 */
	public function isFit(Circumstance $circumstance):bool {

		return true;
	}

	/**
	 * Возвращает, соответствует ли текущее состояние опции пробному периоду.
	 */
	public function isTrial(int $time):bool {

		return false;
	}

	/**
	 * Проверяет, не накладывает ли опция ограничений.
	 */
	public function isRestricted(int $time, int $delta):bool {

		return false;
	}

	/**
	 * Является ли значение опции исходным.
	 * Вызывается из плана при необходимости проверить изменения.
	 */
	abstract public function isSame(self $to_assert):bool;

	/**
	 * Экспортирует данные для сохранения.
	 */
	#[\JetBrains\PhpStorm\ArrayShape(["rule" => "string"])]
	abstract public function export():array;

	/**
	 * Возвращает функцию-загрузчик, для генерации
	 * актуальных параметров конструктора опции.
	 *
	 * На вход получает набор данных по-умолчанию,
	 * который будет использоваться, если походящих данных
	 * в загруженных записях не найдется.
	 *
	 * Должна возвращать функцию, принимающую на вход строку из базы
	 * и возвращающую массив с данными конструктора опции.
	 */
	public static function makeLoadFn(array $data):callable {

		$output = $data;
		$carry  = 0;

		return static function(array $row) use (&$carry, &$output) {

			if (!isset($row["option_list"][static::OPTION_NAME])) {
				return $output;
			}

			$passed_id = (int) $row["id"];

			if ($passed_id > $carry) {

				$carry  = $passed_id;
				$output = array_merge($output, $row["option_list"][static::OPTION_NAME]);
			}

			return $output;
		};
	}
}
