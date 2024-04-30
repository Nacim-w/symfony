<?php
namespace Shake\Database\Orm;

use Nette,
	Nette\SmartObject;


/**
 * Shake\Database\Orm\Context
 * Enhanced Nette\Database\Context with lightweight ORM features and more.
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class Context
{
	use SmartObject;

	/** @var Nette\Database\Context */
	private $context;

	/** @var IFactory */
	private $factory;

	/** @var int $transactionDepth  Depth for nested transactions */
	private $transactionDepth = 0;

	/** @var boolean state of the DB connection, if it is already used */
	private $active = FALSE;



	/**
	 * @param Nette\Database\Context
	 * @param IFactory
	 */
	public function __construct(Nette\Database\Context $context, IFactory $factory)
	{
		$this->context = $context;
		$this->factory = $factory;
	}


	protected function activate()
	{
		$this->context->query('SET sql_mode = "";');  // turn off sql_mode=ONLY_FULL_GROUP_BY

		$this->active = TRUE;
	}



	/********************* ORM *********************/



	/**
	 * @param string
	 * @return Shake\Database\Orm\Table
	 */
	public function table($table)
	{
		if (!$this->active) {
			$this->activate();
		}

		$table = $this->context->table($table);

		return $this->factory->createTable($table);
	}



	/**
	 * @param string
	 * @param array|NULL
	 * @return mixed
	 */
	public function __call($name, $args = array())
	{
		if (!$this->active) {
			$this->activate();
		}

		return call_user_func_array(array($this->context, $name), $args);
	}



	/********************* Transactions *********************/



	/**
	 * @return bool
	 */
	public function beginTransaction()
	{
		$this->transactionDepth++;

		if ($this->transactionDepth == 1) {
			return $this->context->beginTransaction();
		} else {
			return TRUE;
		}
	}



	/**
	 * @return bool
	 */
	public function commit()
	{
		$this->transactionDepth--;

		if ($this->transactionDepth == 0) {
			return $this->context->commit();
		} else {
			return TRUE;
		}
	}



	/**
	 * @return bool
	 */
	public function rollBack()
	{
		$this->transactionDepth = 0;

		return $this->context->rollBack();
	}

}
