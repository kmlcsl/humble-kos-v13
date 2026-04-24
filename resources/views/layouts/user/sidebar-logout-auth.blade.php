<form method="POST" action="{{ route('logout') }}" class="logout-form">
    @csrf
    <button type="submit" class="logout-button">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
    </button>
</form>
