<?php declare(strict_types=1);

namespace Tariff\Plan;

/**
 * Описывает результат применения альтерации тарифного плана.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
final class AlterationResult {

	/**
	 * Конструктор.
	 */
	public function __construct(protected int $_code = 0, protected string $_message = "") {

	}

	/**
	 * Возвращает статус успешности применения альтерации.
	 */
	public function isSuccess():bool {

		return $this->_code === 0;
	}

	/**
	 * Возвращает код ошибки.
	 */
	public function getCode():int {

		return $this->_code;
	}

	/**
	 * Возвращает сопровождающее сообщение.
	 */
	public function getMessage():string {

		return $this->_message;
	}
}
