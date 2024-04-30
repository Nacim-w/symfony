<?php
namespace Shake\Database\Orm;

use Nette,
	Nette\SmartObject,
	Nette\Database\Table\IRowContainer;


/**
 * Shake\Database\Orm\Table
 * Enhanced Nette\Database\Table\Selection with lightweight ORM features.
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class Table implements \Iterator, IRowContainer, \ArrayAccess, \Countable
{
	use SmartObject;

	/** @var Nette\Database\Table\Selection */
	private $selection;

	/** @var IFactory  Factory for creating ORM objects */
	private $factory;



	/**
	 * @param Nette\Database\Table\Selection
	 * @param IFactory
	 */
	public function __construct(Nette\Database\Table\Selection $selection, IFactory $factory)
	{
		$this->selection = $selection;
		$this->factory = $factory;
	}



	/**
	 * @return Nette\Database\Table\Selection
	 */
	public function getSelection()
	{
		return $this->selection;
	}



	/********************* ORM *********************/



	/**
	 * @param string
	 * @return Shake\Database\Orm\Entity|FALSE
	 */
	public function get($key)
	{
		$result = $this->selection->get($key);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createEntity($result);
		} else {
			return $result;
		}
	}



	/**
	 * @return Shake\Database\Orm\Entity|FALSE
	 */
	public function fetch()
	{
		$result = $this->selection->fetch();

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createEntity($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param string|NULL
	 * @param mixed|NULL
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
	{
		return $this->selection->fetchPairs($key, $value);
	}



	/**
	 * @return Shake\Database\Orm\Entity[]
	 */
	public function fetchAll()
	{
		$rows = $this->selection->fetchAll();

		$fetchAll = array();
		foreach ($rows as $row) {
			$fetchAll[] = $this->factory->createEntity($result);
		}

		return $fetchAll;
	}



	/**
	 * @param string
	 * @return array
	 */
	public function fetchAssoc($path)
	{
		return $this->selection->fetchAssoc($path);
	}



	/**
	 * @param string
	 * @return self
	 */
	public function select($columns)
	{
		call_user_func_array(array($this->selection, 'select'), func_get_args());

		return $this;
	}



	/**
	 * @param mixed
	 * @return self
	 */
	public function wherePrimary($key)
	{
		$this->selection->wherePrimary($key);

		return $this;
	}



	/**
	 * @param string
	 * @param mixed
	 * @return self
	 */
	public function where($condition, $parameters = array())
	{
		call_user_func_array(array($this->selection, 'where'), func_get_args());

		return $this;
	}



	/**
	 * @param mixed
	 * @return self
	 */
	public function whereOr($parameters = array())
	{
		call_user_func_array(array($this->selection, 'whereOr'), func_get_args());

		return $this;
	}



	/**
	 * @param string
	 * @return self
	 */
	public function order($columns)
	{
		call_user_func_array(array($this->selection, 'order'), func_get_args());

		return $this;
	}



	/**
	 * @param int
	 * @param int|NULL
	 * @return self
	 */
	public function limit($limit, $offset = NULL)
	{
		$this->selection->limit($limit, $offset);

		return $this;
	}



	/**
	 * @param int
	 * @param int
	 * @param mixed|NULL
	 * @return self
	 */
	public function page($page, $itemsPerPage, & $numOfPages = NULL)
	{
		$this->selection->page($page, $itemsPerPage, $numOfPages);

		return $this;
	}



	/**
	 * @param string
	 * @return self
	 */
	public function group($columns)
	{
		call_user_func_array(array($this->selection, 'group'), func_get_args());

		return $this;
	}



	/**
	 * @param string
	 * @return self
	 */
	public function having($having)
	{
		call_user_func_array(array($this->selection, 'having'), func_get_args());

		return $this;
	}



	/**
	 * @param array|\Traversable|Nette\Database\Table\Selection
	 * @return Shake\Database\Orm\Entity|bool|int
	 */
	public function insert($data)
	{
		$result = $this->selection->insert($data);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createEntity($result);
		} else {
			return $result;
		}
	}



	/**
     * Aliases table. Example ':book:book_tag.tag', 'tg'
     * @param  string
     * @param  string
     * @return static
     */
	public function alias($tableChain, $alias)
	{
		call_user_func_array(array($this->selection, 'alias'), func_get_args());

		return $this;
	}



	/**
	 * @param string
	 * @param string
	 * @param string
	 * @return Shake\Database\Orm\Table|array
	 */
	public function getReferencedTable($table, $column, $checkPrimaryKey)
	{
		$result = $this->selection->getReferencedTable($table, $column, $checkPrimaryKey);

		if ($result instanceof Nette\Database\Table\IRowContainer) {
			return $this->factory->createTable($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param string
	 * @param array|NULL
	 * @return mixed
	 */
	public function __call($name, $args = array())
	{
		return call_user_func_array(array($this->selection, $name), $args);
	}



	/**
	 * @return void
	 */
	public function __clone()
	{
		$this->selection = clone $this->selection;
	}



	/********************* interface Countable *********************/



	/**
	 * @param  string|NULL
	 * @return int
	 */
	public function count($column = NULL)
	{
		return $this->selection->count();
	}



	/********************* interface Iterator *********************/



	/**
	 * @return void
	 */
	public function rewind()
	{
		$this->selection->rewind();
	}



	/**
	 * @return Shake\Database\Orm\Entity|FALSE
	 */
	public function current()
	{
		$result = $this->selection->current();

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createEntity($result);
		} else {
			return $result;
		}
	}



	/**
	 * @return string
	 */
	public function key()
	{
		return $this->selection->key();
	}



	/**
	 * @return void
	 */
	public function next()
	{
		$this->selection->next();
	}



	/**
	 * @return bool
	 */
	public function valid()
	{
		return $this->selection->valid();
	}



	/********************* interface ArayAccess *********************/



	/**
	 * @param string
	 * @param Nette\Database\Table\IRow
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->selection->offsetSet($key, $value);
	}



	/**
	 * @param string
	 * @return Shake\Database\Orm\Entity|NULL
	 */
	public function offsetGet($key)
	{
		$result = $this->selection->offsetGet($key);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createEntity($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param string
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->selection->offsetExists($key);
	}



	/**
	 * @param string
	 * @return void
	 */
	public function offsetUnset($key)
	{
		$this->selection->offsetUnset($key);
	}

}
