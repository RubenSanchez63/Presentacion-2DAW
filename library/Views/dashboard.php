<?php
include __DIR__ . '/../Views/layout/header.php';

use Clases\Sesion;
// Colour palette for category badges
$categoryColors = [
    'Literature'       => ['bg' => '#2d6a4f', 'badge' => '#d4edda', 'text' => '#155724'],
    'Science Fiction'  => ['bg' => '#1a2f4a', 'badge' => '#cce5ff', 'text' => '#004085'],
    'History'          => ['bg' => '#6b4c11', 'badge' => '#fff3cd', 'text' => '#856404'],
    'Technology'       => ['bg' => '#1d3557', 'badge' => '#d0e8f7', 'text' => '#0c3547'],
    'Classic Literature' => ['bg' => '#4a1942', 'badge' => '#f3d7f8', 'text' => '#6b106e'],
    'Mathematics'      => ['bg' => '#7b2d00', 'badge' => '#fde8d8', 'text' => '#7b2d00'],
    'Law'              => ['bg' => '#2c3e50', 'badge' => '#d5d8dc', 'text' => '#1a252f'],
    'Medicine'         => ['bg' => '#922b21', 'badge' => '#fadbd8', 'text' => '#922b21'],
    'Art & Design'     => ['bg' => '#6d4c41', 'badge' => '#efebe9', 'text' => '#4e342e'],
];
$defaultColor = ['bg' => '#2d6a4f', 'badge' => '#d4edda', 'text' => '#155724'];
?>

<!-- ─── Search & Actions Bar ───────────────────────────────── -->
<div class="flex flex-wrap gap-3 mb-8">
    <div class="relative flex-1 min-w-60">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input id="searchInput" type="text"
            placeholder="Search by title, author, or ISBN..."
            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-600">
    </div>
    <select id="categoryFilter"
        class="bg-white border border-slate-200 text-sm px-4 py-2.5 rounded-xl shadow-sm focus:outline-none">
        <option value="">All Categories</option>
        <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
        <?php endforeach;
        endif; ?>
    </select>
    <?php if (Sesion::getRole() === 'admin' or Sesion::getRole() === 'librarian'): ?>
        <button onclick="openModal('addBookModal')"
            class="text-white text-sm px-5 py-2.5 rounded-xl font-semibold flex items-center gap-2 shadow-sm transition"
            style="background-color:#2d6a4f;"
            onmouseover="this.style.backgroundColor='#245a42'"
            onmouseout="this.style.backgroundColor='#2d6a4f'">
            <i class="fas fa-plus"></i> Add Book
        </button>
    <?php endif; ?>
</div>

<!-- ─── Stats Cards ───────────────────────────────────────── -->
<?php if (Sesion::getRole() === 'admin' or Sesion::getRole() === 'librarian'): ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
        <?php foreach ($stats as $label => $value): ?>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-2"><?= htmlspecialchars($label) ?></p>
                <p class="text-4xl font-bold" style="color:#1a2f4a; font-family:'Playfair Display',serif;"><?= htmlspecialchars($value) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<!-- ─── Book Grid ─────────────────────────────────────────── -->
<?php if (empty($books)): ?>
    <div class="text-center py-20 text-slate-400">
        <i class="fas fa-books text-5xl mb-4 opacity-30"></i>
        <p class="text-lg font-medium">No books found</p>
        <p class="text-sm mt-1">Add your first book using the button above</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($books as $book):
            $catName = $book->categoryName ?? 'Literature';
            $colors  = $categoryColors[$catName] ?? $defaultColor;
            $hasCover = !empty($book->cover) && $book->cover !== 'default.jpg';
        ?>
            <div class="book-card bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 flex flex-col transition hover:shadow-md hover:-translate-y-0.5"
                style="transition: all 0.2s ease;"
                data-title="<?= htmlspecialchars($book->title) ?>"
                data-author="<?= htmlspecialchars($book->author) ?>"
                data-category="<?= $book->category_id ?? '' ?>">

                <!-- Cover -->
                <div class="relative h-44 flex items-center justify-center overflow-hidden cursor-pointer"
                    style="background-color:<?= $colors['bg'] ?>;"
                    onclick="openBookDetail(<?= $book->id ?>)">
                    <?php if ($hasCover): ?>
                        <img src="assets/covers/<?= htmlspecialchars($book->cover) ?>"
                            alt="<?= htmlspecialchars($book->title) ?>"
                            class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-file-alt text-white text-5xl opacity-70"></i>
                    <?php endif; ?>

                    <!-- Category badge -->
                    <span class="absolute top-2.5 right-2.5 text-xs font-semibold px-2.5 py-1 rounded-full"
                        style="background-color:<?= $colors['badge'] ?>; color:<?= $colors['text'] ?>;">
                        <?= htmlspecialchars($catName) ?>
                    </span>

                    <!-- PDF label -->
                    <span class="absolute bottom-2.5 left-2.5 text-[10px] font-bold px-2 py-0.5 rounded"
                        style="background-color:rgba(0,0,0,0.45); color:white;">
                        PDF
                    </span>


                </div>

                <!-- Info -->
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-bold text-base leading-tight mb-1 line-clamp-2 cursor-pointer hover:underline"
                        style="color:#1a2f4a;"
                        onclick="openBookDetail(<?= $book->id ?>)">
                        <?= htmlspecialchars($book->title) ?>
                    </h3>
                    <p class="text-slate-400 text-sm mb-4"><?= htmlspecialchars($book->author) ?></p>

                    <div class="mt-auto space-y-2">
                        <a href="assets/pdfs/<?= htmlspecialchars($book->pdfPath ?? '') ?>" target="_blank"
                            class="w-full text-white py-2 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm transition"
                            style="background-color:#1a2f4a;"
                            onmouseover="this.style.backgroundColor='#132238'"
                            onmouseout="this.style.backgroundColor='#1a2f4a'">
                            <i class="fas fa-book-open text-xs"></i> Read Book
                        </a>
                        <?php if (Sesion::getRole() === 'admin' or Sesion::getRole() === 'librarian'): ?>
                            <div class="flex gap-2">
                                <button onclick="openEditModal(<?= $book->id ?>)"
                                    class="flex-1 border border-slate-200 text-slate-600 py-1.5 rounded-xl text-xs font-semibold hover:bg-slate-50 transition">
                                    Edit
                                </button>
                                <button onclick="deleteBook(<?= $book->id ?>, '<?= addslashes(htmlspecialchars($book->title)) ?>')"
                                    class="flex-1 border text-xs font-semibold py-1.5 rounded-xl transition"
                                    style="border-color:#fecaca; color:#ef4444;"
                                    onmouseover="this.style.backgroundColor='#fef2f2'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <i class="fas fa-trash-alt mr-1"></i> Delete
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif;
include __DIR__ . '/../Views/layout/footer.php';
?>