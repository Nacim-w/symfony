<?php
declare(strict_types=1);

namespace Shake\Database\DI;

use Nette;


/**
 * Database\DI\Extension
 *
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 * @package Shake
 */
class Extension extends Nette\DI\CompilerExtension
{

	// TODO add conventional factory settings to config.neon

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('conventionalFactory'))
			->setFactory(
				'Shake\Database\Orm\ConventionalFactory',
				['App\Model\*Entity', 'App\Model\*Table']
			);

		$builder->addDefinition($this->prefix('context'))  // -> shake.database.context
			->setFactory(
				'Shake\Database\Orm\Context',
				[
					'@nette.database.default.context',
					'@' . $this->prefix('conventionalFactory'),
				]
			);
	}

}
