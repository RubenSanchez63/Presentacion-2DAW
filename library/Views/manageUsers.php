<?php
use Clases\Sesion;

// Verificar que es admin
if (Sesion::getRole() !== 'admin') {
    header('Location: /library/');
    exit;
}
?>

<?php require_once __DIR__ . '/layout/header.php'; ?>

<div class="py-8">
    <div class="flex justify-between items-center mb-8">
        <h2 class="brand-font text-3xl font-bold" style="color:#1a2f4a;">Manage Users</h2>
        <a href="/library/users/create"
           class="text-white text-sm px-4 py-2 rounded-md flex items-center gap-2 transition"
           style="background-color:#10b981;"
           onmouseover="this.style.backgroundColor='#059669'"
           onmouseout="this.style.backgroundColor='#10b981'">
            <i class="fas fa-plus text-sm"></i>
            Create User
        </a>
    </div>

    <!-- Tabla de usuarios -->
    <div class="overflow-x-auto rounded-lg shadow-md" style="background-color:#ffffff;">
        <table class="w-full border-collapse">
            <thead>
                <tr style="background-color:#1a2f4a;">
                    <th class="px-6 py-4 text-left text-white font-semibold">ID</th>
                    <th class="px-6 py-4 text-left text-white font-semibold">Name</th>
                    <th class="px-6 py-4 text-left text-white font-semibold">Email</th>
                    <th class="px-6 py-4 text-left text-white font-semibold">Role</th>
                    <th class="px-6 py-4 text-left text-white font-semibold">Registered</th>
                    <th class="px-6 py-4 text-center text-white font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['id']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['name']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                              style="background-color: <?= $user['role'] === 'admin' ? '#fecaca' : ($user['role'] === 'librarian' ? '#dbeafe' : '#bbf7d0') ?>; 
                                     color: <?= $user['role'] === 'admin' ? '#7f1d1d' : ($user['role'] === 'librarian' ? '#1e40af' : '#065f46') ?>;">
                            <?= htmlspecialchars(ucfirst($user['role'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= date('d/m/Y', strtotime($user['createdAt'])) ?></td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="/library/users/edit/<?= $user['id'] ?>"
                               class="text-white text-xs px-3 py-1 rounded transition"
                               style="background-color:#3b82f6;"
                               onmouseover="this.style.backgroundColor='#2563eb'"
                               onmouseout="this.style.backgroundColor='#3b82f6'">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button onclick="deleteUser(<?= $user['id'] ?>)"
                                    class="text-white text-xs px-3 py-1 rounded transition"
                                    style="background-color:#ef4444;"
                                    onmouseover="this.style.backgroundColor='#dc2626'"
                                    onmouseout="this.style.backgroundColor='#ef4444'">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (empty($users)): ?>
    <div class="mt-8 p-8 text-center rounded-lg" style="background-color:#f3f4f6;">
        <i class="fas fa-inbox text-4xl" style="color:#9ca3af;"></i>
        <p class="mt-4 text-gray-600">There are no users registered</p>
    </div>
    <?php endif; ?>
</div>

<script>
function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    fetch(`/library/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.result || data.success) {
            alert('User deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Could not delete the user'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting the user');
    });
}
</script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
