<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Language</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>🌍 Language Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Current Language Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>App Locale:</strong> {{ app()->getLocale() }}</p>
                        <p><strong>Session Locale:</strong> {{ session('locale', 'Not set') }}</p>
                        <p><strong>Config Locale:</strong> {{ config('app.locale') }}</p>
                        <p><strong>Fallback Locale:</strong> {{ config('app.fallback_locale') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Language Switcher</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('language.switch', 'ja') }}" class="btn btn-primary me-2">🇯🇵 日本語</a>
                        <a href="{{ route('language.switch', 'vi') }}" class="btn btn-success">🇻🇳 Tiếng Việt</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Translation Test</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Dashboard:</strong> {{ __('messages.dashboard') }}</p>
                        <p><strong>Restaurant:</strong> {{ __('messages.restaurant') }}</p>
                        <p><strong>Login:</strong> {{ __('messages.login') }}</p>
                        <p><strong>Register:</strong> {{ __('messages.register') }}</p>
                        <p><strong>Hero Title:</strong> {{ __('messages.hero_title') }}</p>
                        <p><strong>Hero Subtitle:</strong> {{ __('messages.hero_subtitle') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">← Back to Welcome</a>
        </div>
    </div>
</body>
</html>
