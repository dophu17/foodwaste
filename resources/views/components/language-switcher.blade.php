<div class="language-switcher dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @if(app()->getLocale() == 'ja')
            🇯🇵 日本語
        @else
            🇻🇳 Tiếng Việt
        @endif
    </button>
            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('language.switch', 'ja') }}">
                    🇯🇵 日本語
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('language.switch', 'vi') }}">
                    🇻🇳 Tiếng Việt
                </a>
            </li>
        </ul>
</div>
