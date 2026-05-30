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
        <h2 class="brand-font text-3xl font-bold mb-6" style="color:#1a2f4a;">Create New User</h2>

        <form id="createUserForm" class="space-y-6">
            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    Name <span style="color:#ef4444;">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       placeholder="John Doe"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition"
                       style="focus-background-color:#f3f4f6;"
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
                       placeholder="john@example.com"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition"
                       onfocus="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f9fafb'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#ffffff'">
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    Password <span style="color:#ef4444;">*</span>
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Minimum 8 characters"
                       required
                       minlength="8"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none transition"
                       onfocus="this.style.borderColor='#4f46e5'; this.style.backgroundColor='#f9fafb'"
                       onblur="this.style.borderColor='#d1d5db'; this.style.backgroundColor='#ffffff'">
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="confirmPassword" class="block text-sm font-semibold mb-2" style="color:#1a2f4a;">
                    Confirm Password <span style="color:#ef4444;">*</span>
                </label>
                <input type="password"
                       id="confirmPassword"
                       name="confirmPassword"
                       placeholder="Repeat the password"
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
                    <option value="">-- Select a role --</option>
                    <option value="user">User</option>
                    <option value="librarian">Librarian</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <!-- Errores -->
            <div id="errorContainer" style="display:none;" class="p-4 rounded-md" style="background-color:#fee2e2; color:#991b1b;">
                <p id="errorMessage" class="text-sm font-semibold"></p>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-4">
                <button type="submit"
                        class="flex-1 text-white text-sm px-4 py-2.5 rounded-md font-semibold transition"
                        style="background-color:#10b981;"
                        onmouseover="this.style.backgroundColor='#059669'"
                        onmouseout="this.style.backgroundColor='#10b981'">
                    <i class="fas fa-save"></i> Create User
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
document.getElementById('createUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const role = document.getElementById('role').value;

    // Validaciones
    if (!name || !email || !password || !role) {
        showError('All fields are required');
        return;
    }

    if (password.length < 8) {
        showError('The password must have at least 8 characters');
        return;
    }

    if (password !== confirmPassword) {
        showError('The passwords do not match');
        return;
    }

    try {
        const response = await fetch('/library/users/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name,
                email,
                password,
                role
            })
        });

        const data = await response.json();

        if (response.ok && (data.success || data.result)) {
            alert('User created successfully');
            window.location.href = '/library/users/manage';
        } else {
            showError(data.error || 'Error creating the user');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Connection error while creating the user');
    }
});

function showError(message) {
    const errorContainer = document.getElementById('errorContainer');
    const errorMessage = document.getElementById('errorMessage');
    errorMessage.textContent = message;
    errorContainer.style.display = 'block';
    errorContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
