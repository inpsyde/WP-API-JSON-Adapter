<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Iterator;
use WPAPIAdapter\Core;
use WPAPIAdapter\Field;

/**
 * The FieldsOperator iterates over
 *
 * @package WPAPIAdapter\Iterator
 */
class EntityFieldsIterator implements \Iterator, \ArrayAccess, Core\FieldProcessorInterface {

	/**
	 * @type \stdClass
	 */
	private $entity;

	/**
	 * @type \stdClass
	 */
	private $original_entity;

	/**
	 * @type \ArrayIterator
	 */
	private $iterator;

	/**
	 * @type Core\FieldHandlerRepository
	 */
	private $handler_repo;

	/**
	 * @type int
	 */
	private $index = 0;

	/**
	 * @param \stdClass $entity
	 * @param Core\FieldHandlerRepository $handler_repo
	 */
	function __construct( \stdClass $entity, Core\FieldHandlerRepository $handler_repo ) {

		$this->entity = $entity;
		$this->original_entity = clone $entity ;

		$this->iterator = new \ArrayIterator(
			new \ArrayObject( $this->entity )
		);

		$this->handler_repo = $handler_repo;
	}

	/**
	 * Process a single field identified by $this->key().
	 *
	 * @return bool
	 */
	public function process_field() {

		$field = $this->key();
		$handlers = $this->handler_repo->get_handlers( $field );
		if ( empty( $handlers ) )
			return FALSE;

		/* @type Field\FieldHandlerInterface $handler */
		foreach( $handlers as $handler ) {
			// cloning to avoid internal changes affect handlers among each other
			$handler->set_original_entity( clone $this->original_entity );
			$handler->handle( $this->current() );

			// delete the key if the name is empty
			if ( ! $handler->get_name() ) {
				$this->iterator->offsetUnset( $field );
				// set the index to the ancestor field because next() will be move it to the correct position
				$this->index--;
				$this->iterator->seek( $this->index );
				// stop invoking more handlers if this one dropped the field
				break;
			// rename the field
			}
			if ( $handler->get_name() !== $field ) {
				$this->iterator->offsetUnset( $field );
				$this->iterator->offsetSet( $handler->get_name(), $handler->get_value() );
				// set the index to the ancestor field because next() will be move it to the correct position
				$this->index--;
				$this->iterator->seek( $this->index );
				// stop invoking more handlers if the field got renamed
				break;
			}

			$this->iterator->offsetSet( $field, $handler->get_value() );
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
		$this->index ++;
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
		$this->index = 0;
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