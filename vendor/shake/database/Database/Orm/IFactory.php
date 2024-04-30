<?php
namespace Shake\Database\Orm;

use Nette;


/**
 * Shake\Database\Orm\IFactory
 * Factory for ORM objects creation.
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
interface IFactory
{

	/**
	 * @param Nette\Database\Table\Selection
	 * @return Shake\Database\Orm\Table
	 */
	public function createTable(Nette\Database\Table\Selection $selection);



	/**
	 * @param Nette\Database\Table\ActiveRow
	 * @return Shake\Database\Orm\Entity
	 */
	public function createEntity(Nette\Database\Table\ActiveRow $row);

}
