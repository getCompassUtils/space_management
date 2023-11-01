<?php declare(strict_types=1);

namespace Tariff\Plan\MemberCount\Default;

/**
 * Тарифный план числа пользователей,
 * доступных в пространстве по умолчанию.
 */
class Plan extends \Tariff\Plan\MemberCount\BasePlan {

	/** @var int уникальный идентификатор плана */
	public const PLAN_ID = 1001;
}