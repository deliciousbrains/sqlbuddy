<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>SQL Buddy - @yield('title')</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css"
          integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}">
</head>
<body id="sqlbuddy">
	<div class="sqlbuddy-nav">
        @include('common.nav')
	</div>

    <sqlbuddy inline-template>
        <div class="sqlbuddy" :class="{ 'sqlbuddy-isloading': isLoading }">
            <div class="sqlbuddy-sidebar">
                @include('common.sidebar')
            </div>
            <div class="sqlbuddy-main">
                <div class="alert alert-danger alert-dismissible" v-if="error">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    @{{ error }}
                </div>

                @yield('content')
            </div>
	        <div class="sqlbuddy-loading-overlay" v-show="isLoading">
		        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
		        <span class="sr-only">Loading...</span>
	        </div>
        </div>
    </sqlbuddy>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.2/js/tether.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js"
            integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7"
            crossorigin="anonymous"></script>
    <script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>