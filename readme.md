# Easy Asset Management in Laravel (Alpha)

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

## Creating a Guardfile

The first step is to install the necessary dependencies (done automatically for you), and create the Guardfile. Run `php artisan guard:make` to do this as quickly as possible. Once you answer the questions, a new `Guardfile` will be added to the root of your project.

## Configuring Paths

Unless you specify custom paths for these directories, LaravelGuard will use sensible defaults.

- **Sass**: app/assets/sass
- **Less**: app/assets/less
- **CoffeeScript**: app/assets/coffee
- **JS**: public/js
- **CSS**: public/css

To override these defaults, publish the default configuration options to your `app/config` directory.

```bash
php artisan config:publish way/guard-laravel
```

You may now edit these options at `app/config/packages/way/guard-laravel/guard.php`.


## Sass/Less/CoffeeScript Compilation

After you've generated a Guardfile with `php artisan guard:make` (which will also download any necessary dependencies), you'll see a new `app/assets` directory. This is where your Sass/Less/CoffeeScript files will be store. Try creating a new file, `app/assets/sass/buttons.sass`, and add:

```css
.button
  background: red
```

If you save the file, nothing will happen. We have to tell Guard to begin watching the filesystem. Run `php artisan guard:watch` to do so. Now, save the file again, and you'll find the compiled output within `public/css/buttons.css`. This same process will be true for compiling Less (if you choose that option) as well as CoffeeScript.


## Concatenation and Minification

By default, when concatenating JavaScript and CSS, this package will simply grab all of the files in their respective directories, and concatenate them in, essentially, random order. Most of the time, this won't be acceptable.

When you need to specify the order, do so in `app/config/packages/way/guard-laravel/guard.php`. Within this file, edit `js_concat` and `css_concat` to contain a list of the files, in order, that you want to merge and minify.

> Every time you update either of these two options, you need to refresh your Guardfile. This allows Guard to pull from the config file, and update the list of files to merge on save.

```bash
php artisan guard:refresh
```

## Continuous Testing

When you run `php artisan guard:watch`, in addition to compiling assets, it will also automatically run your tests when applicable files are saved.

> **Mac Users**: Want native notifications? `gem install terminal-guard-notifier`, and you're all set to go!

Guard will run PHPUnit...

- When any test within `app/tests` is saved, it will auto-run
- When a view file is saved, all tests will run (you may want to update this to only run integration tests)
- When a class is saved, it will attempt to find an associated test and call it. For example, save `app/models/User.php`, and it will test `app/tests/models/UserTest.php`.
-

## Workflow

Here's a basic bit of workflow for a new project. First, install package through Composer. Then:

```bash
php artisan guard:make
php artisan guard:watch

# Edit Sass or CoffeeScript file, and will auto-compile
# Edit a test, and PHPUnit fires

# Update `app/config/packages/way/guard-laravel/guard.php` with your CSS and JS concat order
php artisan guard:refresh
php artisan guard:watch

# Save JS file, and, in the order your specified, JavaScripts will be concatenated and minified.
```
