<?php
use Clases\Sesion;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <title>Libraria e Universitetit "Eqrem Cabej"</title>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .brand-font { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="min-h-screen" style="background-color:#ede8df; color:#1e293b;">

    <!-- Navbar -->
    <nav style="background-color:#1a2f4a;" class="text-white px-8 py-4 flex justify-between items-center shadow-lg">
        <div class="flex items-center gap-3">
            <i class="fas fa-book-open text-xl" style="color:#f6d860;"></i>
            <h1 class="brand-font text-lg font-semibold tracking-wide">
                Libraria e Universitetit "Eqrem Cabej"
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold px-3 py-1 rounded-full" style="background-color:#e8d9a0; color:#7a5c1a;">
                <?= htmlspecialchars(ucfirst(Sesion::user()->role) ?? 'User') ?>
                <?= htmlspecialchars(Sesion::user()->name ?? 'Guest') ?>
            </span>
            <?php if (Sesion::getRole() === 'admin'): ?>
            <a href="/library/users/manage"
               class="text-white text-sm px-4 py-1.5 rounded-md flex items-center gap-2 transition"
               style="background-color:#4f46e5;"
               onmouseover="this.style.backgroundColor='#4338ca'"
               onmouseout="this.style.backgroundColor='#4f46e5'">
                <i class="fas fa-users text-xs"></i>
                Manage Users
            </a>
            <?php endif; ?>
            <a href="/library/logout"
               class="text-white text-sm px-4 py-1.5 rounded-md flex items-center gap-2 transition"
               style="border:1px solid rgba(255,255,255,0.3);"
               onmouseover="this.style.backgroundColor='rgba(255,255,255,0.12)'"
               onmouseout="this.style.backgroundColor='transparent'">
                <i class="fas fa-sign-out-alt text-xs"></i>
                Logout
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 py-8">
