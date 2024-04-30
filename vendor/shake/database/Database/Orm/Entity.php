<?php
namespace Shake\Database\Orm;

use Nette,
	Nette\SmartObject,
	Nette\Utils\ObjectMixin,
	Nette\InvalidStateException,
	Nette\MemberAccessException;


/**
 * Shake\Database\Orm\Entity
 * ORM Entity with support for lazy loading ActiveRow data.
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class Entity implements \IteratorAggregate, Nette\Database\Table\IRow
{
	use SmartObject {
		SmartObject::__set as SmartObject__set;
		SmartObject::__get as SmartObject__get;
		SmartObject::__isset as SmartObject__isset;
		SmartObject::__unset as SmartObject__unset;
	}

	/** @var Nette\Database\Table\ActiveRow */
	private $row;

	/** @var array */
	private $data = array();

	/** @var IFactory  Factory for creating ORM objects */
	private $factory;



	/**
	 * @param Nette\Database\Table\ActiveRow|NULL
	 * @return void
	 */
	public function setRow(Nette\Database\Table\ActiveRow $row)
	{
		$this->row = $row;
	}



	/**
	 * @return Nette\Database\Table\ActiveRow
	 */
	public function getRow()
	{
		if ($this->row) {
			return $this->row;

		} else {
			throw new InvalidStateException("Cant use this until '\$row' is set.");
		}
	}



	/**
	 * @param Shake\Database\Orm\IFactory
	 * @return void
	 */
	public function setFactory(IFactory $factory)
	{
		$this->factory = $factory;
	}



	/**
	 * @return Shake\Database\Orm\IFactory
	 */
	public function getFactory()
	{
		if ($this->factory) {
			return $this->factory;

		} else {
			throw new InvalidStateException("Cant use this until '\$factory' is set.");
		}
	}



	/********************* ORM *********************/



	/**
	 * @param  string
	 * @param  string|NULL
	 * @return Shake\Database\Orm\Entity|NULL
	 */
	public function ref($key, $throughColumn = NULL)
	{
		$result = $this->getRow()->ref($key, $throughColumn);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->getFactory()->createEntity($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param  string
	 * @param  string|NULL
	 * @return Shake\Database\Orm\Table
	 */
	public function related($key, $throughColumn = NULL)
	{
		$selection = $this->getRow()->related($key, $throughColumn);

		return $this->getFactory()->createTable($selection);
	}



	/**
	 * @param string
	 * @param array|NULL
	 * @return mixed
	 */
	public function __call($name, $args = array())
	{
		return call_user_func_array(array($this->getRow(), $name), $args);
	}



	/********************* interface IRow *********************/



	/**
	 * @param Nette\Database\Table\Selection
	 * @return void
	 */
	public function setTable(Nette\Database\Table\Selection $selection)
	{
		$this->getRow()->setTable($selection);
	}



	/**
	 * @return Nette\Database\Table\IRowContainer
	 */
	public function getTable()
	{
		return $this->getRow()->getTable();
	}



	/**
	 * @param bool
	 * @return mixed
	 */
	public function getPrimary($need = TRUE)
	{
		return $this->getRow()->getPrimary($need);
	}



	/**
	 * @param bool
	 * @return string
	 */
	public function getSignature($need = TRUE)
	{
		return $this->getRow()->getSignature($need);
	}



	/********************* interface IteratorAggregate ****************d*g**/



	public function getIterator()
	{
		// Get data
		$data = array();

		if ($this->row) {  // row data
			$data = iterator_to_array($this->getRow());
		}

		foreach ($data as $key => $value) {  // overwrite with get* methods
			if ($this->_has($key)) {
				$data[$key] = $this->_get($key);
			}
		}

		$data = array_merge($data, $this->data);  // overwrite with manually setted values

		// Return iterator
		return new \ArrayIterator($data);
	}



	/********************* interface ArayAccess & magic accessors *********************/



	/**
	 * @param string
	 * @param string
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->__set($key, $value);
	}



	/**
	 * @param string
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->__get($key);
	}



	/**
	 * @param string
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->__isset($key);
	}



	/**
	 * @param string
	 * @return void
	 */
	public function offsetUnset($key)
	{
		$this->__unset($key);
	}



	/**
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}



	/**
	 * @param string
	 * @return mixed
	 */
	public function __get($key)
	{
		// Get data
		if (isset($this->data[$key]))
			return $this->data[$key];  // manually setted data must not be converted to entity

		if ($this->_has($key) || !isset($this->row))
			return $this->_get($key);  // manually created data are mostly entity or table instances

		$result = $this->row->__get($key);

		// Create entity
		if ($result instanceof Nette\Database\Table\IRow && $key != 'row') {  // $entity->row must return original ActiveRow
			$row = $this->getFactory()->createEntity($result);
			return $row;
		} else {
			return $result;
		}
	}



	/**
	 * @param string
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->data[$key])
			|| $this->_has($key)
			|| (isset($this->row) && $this->row->__isset($key));
	}



	/**
	 * @param string
	 * @return void
	 */
	public function __unset($key)
	{
		if (isset($this->data[$key])) {
			unset($this->data[$key]);

		} elseif ($this->_has($key)) {
			throw new InvalidStateException("Can't unset '$key' property method.");

		} elseif (!isset($this->row)) {
			throw new MemberAccessException("Can't unset undeclared property '$key'.");

		} else {
			$this->row->__unset($key);
		}
	}


	protected function _has($key)
	{
		return $this->SmartObject__isset($key);
	}


	protected function _get($key)
	{
		return @$this->SmartObject__get($key);
	}

}
