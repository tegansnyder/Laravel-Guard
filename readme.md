# Easy Asset Management in Laravel

This plugin improves asset management in Laravel, by:

- Compiling Sass or Less files automatically
- Compiling CoffeeScript
- Automatically running tests on save
- Concatenating and minifying JavaScript and CSS (if not using a preprocessor)

## Installation

Install this package through Composer. To your `composer.json` file, add:

```js
"require-dev": {
	"way/guard-laravel": "dev-master"
}
```

Next, run `composer install --dev` to download it.

Finally, add the service provider to `app/config/app.php`, within the `providers` array.

```php
'providers' => array(
	// ...

	'Way\Console\GuardLaravelServiceProvider'
)
```

That's it. Run `php artisan` to view the three new Guard commands:

- **guard:make** - Create a new Guardfile, and specify desired preprocessors
- **guard:watch** - Begin watching filesystem for changes, and run tests
- **guard:refresh** - Refresh your guard file

## Sass Compilation

After you've generated a Guardfile with `php artisan guard:make` (which will also download any necessary dependencies), you'll see a new `app/assets` directory. This is where your Sass/Less/CoffeeScript files will be store. Try creating a new file, `app/assets/sass/buttons.sass`, and add:

```css
.button
  background: red
```

If you save the file, nothing will happen. We have to tell Guard to begin watching the filesystem. Run `php artisan guard:watch` to do so. Now, save the file again, and you'll find the compiled output within `public/css/buttons.css`. This same process will be true for compiling Less (if you choose that option) as well as CoffeeScript.


## Configuring Paths

Unless you specify custom paths (detailed shortly), Laravel Guard will use sensible defaults.

- **Sass**: app/assets/sass
- **Less**: app/assets/less
- **CoffeeScript**: app/assets/coffee
- **JS**: public/js
- **CSS**: public/css


