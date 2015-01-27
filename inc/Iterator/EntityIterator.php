<?php # -*- coding: utf-8 -*-


namespace WPAPIAdapter\Iterator;
use WPAPIAdapter\Field;
use WPAPIAdapter\FieldHandlerRepository;

/**
 * The FieldsOperator iterates over
 *
 * @package WPAPIAdapter\Iterator
 */
class EntityIterator implements \Iterator, FieldsProcessorInterface {

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

		return $this->iterator->next();
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

		return $this->iterator->rewind();
	}
}