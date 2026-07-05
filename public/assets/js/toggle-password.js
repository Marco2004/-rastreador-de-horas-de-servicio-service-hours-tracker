document.addEventListener('DOMContentLoaded', function () {
    const togglePasswordButton = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye');

    togglePasswordButton.addEventListener('click', function () {
        const isPasswordVisible = passwordInput.type === 'text';

        passwordInput.type = isPasswordVisible ? 'password' : 'text';

        eyeIcon.classList.toggle('fa-eye', isPasswordVisible);
        eyeIcon.classList.toggle('fa-eye-slash', !isPasswordVisible);
    });
});
