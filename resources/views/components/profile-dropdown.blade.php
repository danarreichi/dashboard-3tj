<div class="dropdown" id="profileDropdown">
    <a href="#" id="topbarUserDropdown" class="user-dropdown d-flex align-items-center dropend dropdown-toggle "
        data-bs-toggle="dropdown" aria-expanded="false">
        <div class="avatar avatar-md2">
            <img src="{{ asset('dist/assets/compiled/jpg/1.jpg') }}" alt="Avatar">
        </div>
        <div class="text">
            <h6 class="user-dropdown-name" id="profileName"></h6>
            <p class="user-dropdown-status text-sm text-muted" id="profileRole"></p>
        </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
        <li onclick="logout()"><a class="dropdown-item" href="#">Logout</a></li>
    </ul>
</div>
<script>
    function logout() {
        localStorage.removeItem('bearer');
        window.location.href = pageHost + 'login';
    }
</script>
