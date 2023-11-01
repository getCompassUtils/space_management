<?php declare(strict_types=1);

namespace Tariff\Plan;

/**
 * Класс описывающий состояние системы/окружения/пространства,
 * в котором предполагается работа тарифного плана.
 *
 * У каждого типа тарифного плана могут свои настройки окружения,
 * этот класс в качестве родительского нужен для типобезопасности.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
abstract class BaseCircumstance {

}