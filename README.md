# WP API JSON Adapter
Provides interfaces to change the structure of the REST API data.

## Posts

To change the structure of the post objects you have to register a handler for each field you want to change, add or
remove. This handler must be an implementation of the `WPAPIAdapter\Field\FieldHandlerInterface`.

All field handlers are managed by instances of `WPAPIAdapter\Core\FieldHandlerRepository`, handlers to change/remove fields
are handled separately to those which add new fields.

To access the repositories there are two actions provided:
 * `wpapiadapter_register_post_change_field_handler`
 * `wpapiadapter_register_post_add_field_handler`
(Applied in `Core\PostFieldsController::dispatch()`.)

There are already two handler implementations to rename or unset a field:
  * `WPAPIAdapter\Field\RenameFieldHandler`
  * `WPAPIAdapter\Field\UnsetFieldHandler`

## Terms
The handler repositories are provided with the actions:
 * `wpapiadapter_register_term_change_field_handler`
 * `wpapiadapter_register_term_add_field_handler`
(Applied in `Core\TermFieldsController::dispatch()`.)

## Users
The handler repositories are provided with the actions:
 * `wpapiadapter_register_user_change_field_handler`
 * `wpapiadapter_register_user_add_field_handler`
(Applied in `Core\UserFieldsController::dispatch()`.)

## Menus
_Note: Menus are not part of the default WP-API so far. The Adapter looks for a `/wp-json/menus` route._

The handler repositories are provided with the actions:
 * `wpapiadapter_register_menu_change_field_handler`
 * `wpapiadapter_register_menu_add_field_handler`
(Applied in `Core\MenuFieldsController::dispatch()`.)

### Example

Rename the `title` field of the `post` entity to `custom_title`:

```php
add_action(
	'wpapiadapter_register_post_change_field_handler',
	function( WPAPIAdapter\Core\FieldHandlerRepository $repository ) {
		$repository->add_handler( 'title', new WPAPIAdapter\Field\RenameFieldHandler( 'custom_title' );
	}
);
```

Unset the field `status`:

```php
add_action(
	'wpapiadapter_register_post_change_field_handler',
	function( WPAPIAdapter\Core\FieldHandlerRepository $repository ) {
		$repository->add_handler( 'status', new WPAPIAdapter\Field\UnsetFieldHander );
	}
);
```

Rename and restructure the field `author` to `author_ID`. The data structure before:

```
{
	"author" : {
		"ID" : 1,
		"name" : "John Doe"
	}
}
```
and after:
```
{
	"author_ID" : 1
}
```

Code:
```php
add_action(
	'wpapiadapter_register_post_change_field_handler',
	function( WPAPIAdapter\Core\FieldHandlerRepository $repository ) {
		$repository->add_handler( 'author', new MyCustomAuthorField );
	}
);

class MyCustomAuthorField implements WPAPIAdapter\Field\FieldHandlerInterface {

	/**
	 * @type int
	 */
	private $value;

	/**
	 * @type \stdClass
	 */
	private $original_entity;

	/**
     * @return string
     */
    public function get_name() {

        // return the new name
        return 'author_ID';
    }

    /**
     * @return mixed
     */
    public function get_value() {

        return $this->value;
    }

    /**
     * @param \WP_JSON_Server $server
     * @return void
     */
    public function set_server( \WP_JSON_Server $server ) {

        // maybe store the $server instance internally
    }


    /**
     * @param $field (any primitive value str,array,\stdClass,…)
     * @return void
     */
    public function handle( $field ) {

        // handle the original field value here
        if ( isset( $field[ 'ID' ] ) )
            $this->value = $field[ 'ID' ];
        else
            $this->value = $field; //pass the original through if there is an unexpected structure

        //if you need access to the post e.g. use $this->original_entity->ID
    }

    /**
     * @param \stdClass $original_entity
     * @return void
     */
    public function set_original_entity( \stdClass $original_entity ) {

        $this->original_entity = $original_entity;
    }
}
```



## Other Notes
###Crafted by [Inpsyde](http://inpsyde.com) · Engineering the web since 2006.###
Yes, we also run that [marketplace for premium WordPress plugins and themes](http://marketpress.com).

### License
Good news, this plugin is free for everyone! Since it's released under the MIT license, 
you can use it free of charge on your personal or commercial blog.
