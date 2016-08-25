<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>SQL Buddy - @yield('title')</title>

    <link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/font-awesome.min.css') }}">
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

    <script src="{{ url('js/jquery.min.js') }}"></script>
    <script src="{{ url('js/tether.min.js') }}"></script>
    <script src="{{ url('js/bootstrap.min.js') }}"></script>
    <script src="{{ elixir('js/app.js') }}"></script>
</body>
</html>