<?php declare(strict_types=1);

namespace Tariff\Plan\PremiseUserCount;

/**
 * Опция, регулирующая политику ограничения доступа.
 */
#[\JetBrains\PhpStorm\Immutable]
class OptionRestrictPolicy extends Option {

	public const OPTION_NAME = "restrict_policy";

	protected int $_active_from; // с какого времени начинает действовать ограничение

	/**
	 * Конструктор.
	 */
	public function __construct(int $active_from) {

		$this->_active_from = $active_from;
	}

	/**
	 * @inheritDoc
	 */
	public function makeAlterationReplacer(Circumstance $circumstance, Action $action, Dynamic $current_state, Dynamic $expected_state):static {

		if ($expected_state->active_till === 0) {
			return new static(0);
		}

		// в остальных случаях считаем, что опция не должна измениться
		return new static($expected_state->active_till);
	}

	/**
	 * Возвращает флаг наличия ограничений.
	 */
	public function isRestricted(int $time, int $delta):bool {

		// здесь можно как-то посчитать кастомную дельту при необходимости
		return $this->_active_from !== 0 && $time > ($this->_active_from + $delta);
	}

	/**
	 * Возвращает время начала ограничения
	 */
	public function getActiveFrom():int {

		return $this->_active_from;
	}

	/**
	 * Является ли значение опции исходным.
	 * Вызывается из плана при необходимости проверить изменения.
	 */
	public function isSame(Option $to_assert):bool {

		return ($to_assert instanceof $this) && $to_assert->_active_from === $this->_active_from;
	}

	/**
	 * Экспортирует данные для сохранения.
	 */
	#[\JetBrains\PhpStorm\ArrayShape(["active_from" => "int"])]
	public function export():array {

		return ["active_from" => $this->_active_from];
	}
}
