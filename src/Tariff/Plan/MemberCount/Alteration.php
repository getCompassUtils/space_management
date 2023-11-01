<?php declare(strict_types=1);

namespace Tariff\Plan\MemberCount;

use Tariff\Plan\AlterationAvailability;
use Tariff\Plan\BaseAlteration;

/**
 * Описательная структура для генерации действия в будущем.
 * Как правило должна привязываться к goods_id для последующей активации.
 */
#[\JetBrains\PhpStorm\Immutable(\JetBrains\PhpStorm\Immutable::PROTECTED_WRITE_SCOPE)]
class Alteration extends BaseAlteration {

	public ?OptionRestrictPolicy $option_restrict_policy = null;
	public ?OptionExtendPolicy   $option_extend_policy   = null;
	public ?OptionLimit          $option_limit           = null;

	public ?OptionRestrictPolicy $expected_option_restrict_policy = null;
	public ?OptionExtendPolicy   $expected_option_extend_policy   = null;
	public ?OptionLimit          $expected_option_limit           = null;

	/**
	 * Устанавливает опцию ограничения числа пользователей.
	 *
	 * <b>Очень важно устанавливать число участников при использовании с goods_id,
	 * иначе тариф будет использовать текущее значение.
	 * Это приведет к тому, что проверка необходимости
	 * оплаты для числа пользователей не будет проходить</b>.
	 */
	public function setMemberCount(int $member_count):static {

		$this->option_limit = new OptionLimit($member_count);
		return $this;
	}

	/**
	 * Добавляет изменения политики автоматического расширения.
	 * <b>С осторожностью использовать в бизнес-сценариях!</b>
	 */
	public function setExtendPolicy(string $extend_policy, int $active_till):static {

		$this->option_extend_policy = new OptionExtendPolicy($extend_policy, $active_till);
		return $this;
	}

	/**
	 * Добавляет изменения политики ограничения доступа.
	 * <b>С осторожностью использовать в бизнес-сценариях!</b>
	 */
	public function setRestrictPolicy(int $start_from):static {

		$this->option_restrict_policy = new OptionRestrictPolicy($start_from);
		return $this;
	}

	/**
	 * Устанавливает проверку числа участников перед активацией.
	 * Нужно для проверки активации продления, что продлевается именно текущий план.
	 */
	public function setExpectedMemberCount(int $member_count):static {

		$this->expected_option_limit = new OptionLimit($member_count);
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function setExtraCondition(callable ...$list):static {

		foreach ($list as $fn) {

			try {

				$reflection = new \ReflectionFunction($fn);
			} catch (\ReflectionException) {
				throw new \RuntimeException("can't get reflection of extra condition");
			}

			$reflection_param_list = $reflection->getParameters();
			$reflection_return     = $reflection->getReturnType();

			if (is_null($reflection_return) || count($reflection_param_list) !== 1) {
				throw new \RuntimeException("extra condition has wrong signature — parameter list or return is incorrect");
			}

			$reflection_param = $reflection_param_list[0];

			// получаем имя параметра
			$reflection_param_type  = $reflection_param->getName();
			$reflection_return_type = $reflection_return->getName();

			if ($reflection_param_type instanceof MemberCount) {
				throw new \RuntimeException("extra condition argument must implement plan interface");
			}

			if ($reflection_return_type instanceof AlterationAvailability) {
				throw new \RuntimeException("extra condition return must be AlterationAvailability instance");
			}
		}

		$this->_extra_conditions = $list;
		return $this;
	}
}
