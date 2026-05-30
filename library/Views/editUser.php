<?php
use Clases\Sesion;

// Verificar que es admin
if (Sesion::getRole() !== 'admin') {
    header('Location: /library/');
    exit;
}
?>

<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="py-8 max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="/library/users/manage"
           class="text-sm px-4 py-2 rounded-md flex items-center gap-2 transition w-fit"
           style="background-color:#6b7280; color:#ffffff;"
           onmouseover="this.style.backgroundColor='#4b5563'"
           onmouseout="this.style.backgroundColor='#6b7280'">
            <i class="fas fa-arrow-left text-xs"></i>
            Back
        </a>
    </div>

    <div style="background-color:#ffffff;" class="p-8 rounded-lg shadow-md">
        <h2 class="brand-font text-3xl font-bold mb-2" style="color:#1a2f4a;">Edit User</h2>
        <p class="text-sm text-gray-500 mb-6">Leave the password fields empty to keep the current password.</p>

        <form id="editUserForm" class="space-y-6">

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    Name <span style="color:#ef4444;">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="<?= htmlspecialchars($user['name']) ?>"
                       placeholder="John Doe"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition"
                       onfocus="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f9fafb'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#ffffff'">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    Email <span style="color:#ef4444;">*</span>
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?= htmlspecialchars($user['email']) ?>"
                       placeholder="john@example.com"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition"
                       onfocus="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f9fafb'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#ffffff'">
            </div>

            <!-- Rol -->
            <div>
                <label for="role" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    Role <span style="color:#ef4444;">*</span>
                </label>
                <select id="role"
                        name="role"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition"
                        onfocus="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f9fafb'"
                        onblur="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#ffffff'">
                    <option value="user"      <?= $user['role'] === 'user'      ? 'selected' : '' ?>>User</option>
                    <option value="librarian" <?= $user['role'] === 'librarian' ? 'selected' : '' ?>>Librarian</option>
                    <option value="admin"     <?= $user['role'] === 'admin'     ? 'selected' : '' ?>>Administrator</option>
                </select>
            </div>

            <!-- Separador sección contraseña -->
            <div class="pt-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex-1 h-px" style="background-color:#e5e7eb;"></div>
                    <span class="text-xs font-semibold uppercase tracking-widest" style="color:#9ca3af;">Set new Password</span>
                    <div class="flex-1 h-px" style="background-color:#e5e7eb;"></div>
                </div>
            </div>

            <!-- Nueva Contraseña -->
            <div>
                <label for="password" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    New Password
                </label>
                <div class="relative">
                    <input type="password"
                           id="password"
                           name="password"
                           placeholder="Minimum 8 characters"
                           minlength="8"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition pr-10"
                           onfocus="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f9fafb'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#ffffff'">
                    <button type="button"
                            onclick="toggleVisibility('password', 'eyeIcon1')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i id="eyeIcon1" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
            </div>

            <!-- Confirmar Nueva Contraseña -->
            <div>
                <label for="confirmPassword" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    Confirm New Password
                </label>
                <div class="relative">
                    <input type="password"
                           id="confirmPassword"
                           name="confirmPassword"
                           placeholder="Repeat the new password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition pr-10"
                           onfocus="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f9fafb'"
                           onblur="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#ffffff'">
                    <button type="button"
                            onclick="toggleVisibility('confirmPassword', 'eyeIcon2')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i id="eyeIcon2" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Errores -->
            <div id="errorContainer" style="display:none; background-color:#fee2e2; color:#991b1b;" class="p-4 rounded-md">
                <p id="errorMessage" class="text-sm font-semibold"></p>
            </div>

            <!-- Éxito -->
            <div id="successContainer" style="display:none; background-color:#d1fae5; color:#065f46;" class="p-4 rounded-md">
                <p id="successMessage" class="text-sm font-semibold"></p>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-4">
                <button type="submit"
                        id="submitBtn"
                        class="flex-1 text-white text-sm px-4 py-2.5 rounded-md font-semibold transition"
                        style="background-color:#4f46e5;"
                        onmouseover="this.style.backgroundColor='#4338ca'"
                        onmouseout="this.style.backgroundColor='#4f46e5'">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="/library/users/manage"
                   class="flex-1 text-white text-sm px-4 py-2.5 rounded-md font-semibold text-center transition"
                   style="background-color:#6b7280;"
                   onmouseover="this.style.backgroundColor='#4b5563'"
                   onmouseout="this.style.backgroundColor='#6b7280'">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const userId = <?= (int)$user['id'] ?>;

document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    hideMessages();

    const name            = document.getElementById('name').value.trim();
    const email           = document.getElementById('email').value.trim();
    const role            = document.getElementById('role').value;
    const password        = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (!name || !email || !role) {
        showError('Name, email and role are required');
        return;
    }

    if (password && password.length < 8) {
        showError('The password must have at least 8 characters');
        return;
    }

    if (password && password !== confirmPassword) {
        showError('The passwords do not match');
        return;
    }

    const payload = { name, email, role };
    if (password) {
        payload.password = password;
    }

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    try {
        const response = await fetch(`/library/users/${userId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (response.ok && (data.success || data.result)) {
            showSuccess('User updated successfully');
            // Clear password fields after success
            document.getElementById('password').value = '';
            document.getElementById('confirmPassword').value = '';
            setTimeout(() => window.location.href = '/library/users/manage', 1500);
        } else {
            showError(data.error || 'Error updating the user');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Connection error while updating the user');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
    }
});

function toggleVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function showError(message) {
    const container = document.getElementById('errorContainer');
    document.getElementById('errorMessage').textContent = message;
    container.style.display = 'block';
    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function showSuccess(message) {
    const container = document.getElementById('successContainer');
    document.getElementById('successMessage').textContent = message;
    container.style.display = 'block';
}

function hideMessages() {
    document.getElementById('errorContainer').style.display   = 'none';
    document.getElementById('successContainer').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
