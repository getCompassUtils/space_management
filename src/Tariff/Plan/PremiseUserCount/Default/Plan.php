<?php declare(strict_types=1);

namespace Tariff\Plan\PremiseUserCount\Default;

/**
 * Лицензия числа пользователей для onpremise-решений.
 */
class Plan extends \Tariff\Plan\PremiseUserCount\BasePlan {

	/** @var int уникальный идентификатор плана */
	public const PLAN_ID = 2001;
}