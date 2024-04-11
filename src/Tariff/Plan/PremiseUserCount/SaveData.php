<?php

namespace Tariff\Plan\PremiseUserCount;

use Tariff\Loader;
use Tariff\Plan\BaseSaveData;

/**
 * Данные для сохранения или экспорта тарифного плана.
 */
#[\JetBrains\PhpStorm\Immutable]
final class SaveData extends BaseSaveData {

	// тип плана выносим отдельно, чтобы никто случайно
	// не задал новую новое значение у уже существующего
	// плана, и не началась путаница с загрузкой
	public int $plan_type = Loader::PREMISE_USER_COUNT_PLAN_ID;
}