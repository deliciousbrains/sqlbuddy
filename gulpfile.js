var elixir = require('laravel-elixir');

require('laravel-elixir-vueify');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
	mix.copy('node_modules/jquery/dist/jquery.min.js', 'public/js')
		.copy('node_modules/tether/dist/js/tether.min.js', 'public/js')
		.copy('node_modules/bootstrap/dist/js/bootstrap.min.js', 'public/js')
		.copy('node_modules/bootstrap/dist/css/bootstrap.min.css', 'public/css')
		.copy('node_modules/bootstrap/dist/css/bootstrap.min.css.map', 'public/css')
		.copy('node_modules/font-awesome/css/font-awesome.min.css', 'public/css')
		.copy('node_modules/font-awesome/fonts', 'public/fonts')
		.browserify('app.js')
		.sass(['app.scss'], 'public/css')
		.version(['js/app.js', 'css/app.css']);
});
