<?php declare(strict_types=1);

namespace Tariff\Plan\MemberCount;

/**
 * Интерфейс тарифного плана числа пользователей.
 * Любой тарифный план «member_count» должен его реализовывать.
 */
interface MemberCount {

	/**
	 * Проверяет наличие изменений с исходными данными.
	 */
	public function hasChanges():bool;

	/**
	 * Возвращает данные для сохранения.
	 */
	public function getData():SaveData;

	/**
	 * Возвращает текущее ограничение числа пользователей.
	 */
	public function getLimit():int;

	/**
	 * Возвращает дату окончания действия.
	 */
	public function getActiveTill():int;

	/**
	 * Проверяет, соответствует ли план указанному состоянию окружения.
	 */
	public function isFit(Circumstance $circumstance):bool;

	/**
	 * Проверяет, действует ли план на текущий момент.
	 */
	public function isActive(int $relative_date):bool;

	/**
	 * Проверяет, доступен ли план сейчас на бесплатной основе.
	 */
	public function isFree(int $time):bool;

	/**
	 * Проверяет, не накладывает ли тариф ограничений.
	 */
	public function isRestricted(int $time, int $delta = 0):bool;

	/**
	 * Пытается выполнить ActivationItem для тарифного плана.
	 * В результате тарифный план может измениться.
	 *
	 * В случае успешного применения возвращает 0.
	 * Если возникает ошибка, то возвращает код ошибки.
	 */
	public function applyAlteration(int $method, Alteration $alteration, Circumstance $circumstance, int $relative_time):\Tariff\Plan\AlterationResult;

	/**
	 * Выводит способ активации для ActivationItem.
	 */
	public function arrangeAlteration(Alteration $alteration, Circumstance $circumstance, int $relative_time, int $method = Action::METHOD_DETACHED):Alteration;

	/**
	 * Возвращает наличие пробного периода на указанное время.
	 */
	public function isTrial(int $relative_time):bool;

	/**
	 * Возвращает доступность пробного периода на указанное время.
	 */
	public function isTrialAvailable(int $relative_time):bool;

	/**
	 * Возвращает правило для расширения пространства
	 */
	public function getExtendPolicyRule():string;

	/**
	 * Получить время начала ограничения
	 */
	public function getRestrictedAccessFrom():int;

	/**
	 * Выполняет загрузку тарифного плана из указанных данных.
	 * Этот метод должен корректно загружать тарифный план из конфига.
	 */
	public static function fromData(int $active_till, int $free_active_till, array $option_data_list):static;

	/**
	 * Выполняет загрузку тарифного плана из набора строк-состояний.
	 * Этот метод должен корректно загружать тарифный план из БД.
	 */
	public static function fromRows(array $row_list):static;

	# endregion методы загрузки
}