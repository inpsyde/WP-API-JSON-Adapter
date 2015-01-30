<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Iterator;
use WPAPIAdapter\Field;
use WPAPIAdapter\Core\FieldHandlerRepository;

/**
 * The FieldsOperator iterates over
 *
 * @package WPAPIAdapter\Iterator
 */
class EntityIterator implements \Iterator, \ArrayAccess, FieldsProcessorInterface {

	/**
	 * @type \stdClass
	 */
	private $entity;

	/**
	 * @type \ArrayIterator
	 */
	private $iterator;

	/**
	 * @type
	 */
	private $handler_repo;

	/**
	 * @param \stdClass              $entity
	 * @param FieldHandlerRepository $handler_repo
	 */
	function __construct( \stdClass $entity, FieldHandlerRepository $handler_repo ) {

		$this->entity = $entity;
		$this->iterator = new \ArrayIterator(
			new \ArrayObject( $this->entity )
		);

		$this->handler_repo = $handler_repo;
	}

	/**
	 * @return bool
	 */
	public function process_field() {

		$key = $this->key();
		$handlers = $this->handler_repo->get_handlers( $key );
		if ( empty( $handlers ) )
			return FALSE;

		/* @type Field\FieldHandlerInterface $handler */
		foreach( $handlers as $handler ) {
			$handler->handle( $this->current() );

			// delete the key if the name is empty
			if ( ! $handler->get_name() ) {
				$this->iterator->offsetUnset( $key );
			// rename the field
			} elseif ( $handler->get_name() !== $key )  {
				$this->iterator->offsetUnset( $key );
				$this->iterator->offsetSet( $handler->get_name(), $handler->get_value() );
			} else {
				$this->iterator->offsetSet( $key, $handler->get_value() );
			}
		}

		return TRUE;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {

		return $this->iterator->current();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {

		$this->iterator->next();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {

		return $this->iterator->key();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 *
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 *       Returns true on success or false on failure.
	 */
	public function valid() {

		return $this->iterator->valid();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {

		$this->iterator->rewind();
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 *                      An offset to check for.
	 *                      </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists( $offset ) {

		return $this->iterator->offsetExists( $offset );
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to retrieve.
	 *                      </p>
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet( $offset ) {

		return $this->iterator->offsetGet( $offset );
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to assign the value to.
	 *                      </p>
	 * @param mixed $value  <p>
	 *                      The value to set.
	 *                      </p>
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {

		$this->iterator->offsetSet( $offset, $value );
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 *                      The offset to unset.
	 *                      </p>
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ) {

		$this->iterator->offsetUnset( $offset );
	}

}