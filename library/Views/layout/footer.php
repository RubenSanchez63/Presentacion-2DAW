    </main>

    <!-- Footer -->
    <footer class="text-center text-sm py-6 mt-12" style="background-color:#1a2f4a; color:rgba(255,255,255,0.55);">
        &copy; <?= date('Y') ?> Universiteti "Eqrem Cabej" Gjirokastër
    </footer>

    <!-- Add Book Modal -->
    <div id="addBookModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background-color:rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-5 flex justify-between items-center" style="background-color:#1a2f4a; color:white;">
                <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Add New Book</h2>
                <button onclick="closeModal('addBookModal')" class="text-white/70 hover:text-white text-xl">&times;</button>
            </div>
            <form id="addBookForm" class="p-6 space-y-4" enctype="multipart/form-data">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Title *</label>
                        <input name="title" type="text" required placeholder="Book title"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2" style="--tw-ring-color:#2d6a4f;">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Author *</label>
                        <input name="author" type="text" required placeholder="Author name"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Category *</label>
                        <select name="category_id" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none bg-white">
                            <option value="">Select category</option>
                            <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Pages</label>
                        <input name="pages" type="number" min="0" placeholder="0"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief description..."
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">PDF File *</label>
                        <input name="pdf" type="file" accept=".pdf" required
                            class="w-full text-sm text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Cover Image</label>
                        <input name="cover" type="file" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 text-white py-2 rounded-lg text-sm font-semibold transition"
                        style="background-color:#2d6a4f;"
                        onmouseover="this.style.backgroundColor='#245a42'"
                        onmouseout="this.style.backgroundColor='#2d6a4f'">
                        <i class="fas fa-upload mr-1"></i> Upload Book
                    </button>
                    <button type="button" onclick="closeModal('addBookModal')"
                        class="flex-1 bg-slate-100 text-slate-600 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div id="editBookModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background-color:rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
            <div class="px-6 py-5 flex justify-between items-center" style="background-color:#1a2f4a; color:white;">
                <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Edit Book</h2>
                <button onclick="closeModal('editBookModal')" class="text-white/70 hover:text-white text-xl">&times;</button>
            </div>
            <form id="editBookForm" class="p-6 space-y-4">
                <input type="hidden" id="editBookId">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Title *</label>
                        <input id="editTitle" type="text" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Author *</label>
                        <input id="editAuthor" type="text" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Category *</label>
                        <select id="editCategory" required
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none">
                            <option value="">Select category</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Pages</label>
                        <input id="editPages" type="number" min="0"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Description</label>
                    <textarea id="editDescription" rows="2"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Cover Image</label>
                    <input id="editCover" type="file" accept="image/*"
                        class="w-full text-sm text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700">
                    <p class="text-xs text-slate-400 mt-1">Leave empty to keep current cover</p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 text-white py-2 rounded-lg text-sm font-semibold transition"
                        style="background-color:#1a2f4a;"
                        onmouseover="this.style.backgroundColor='#132238'"
                        onmouseout="this.style.backgroundColor='#1a2f4a'">
                        Save Changes
                    </button>
                    <button type="button" onclick="closeModal('editBookModal')"
                        class="flex-1 bg-slate-100 text-slate-600 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Book Detail Modal -->
    <div id="bookDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background-color:rgba(0,0,0,0.6);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden flex flex-col" style="max-height:90vh;">
            <!-- Header -->
            <div class="px-6 py-5 flex justify-between items-center flex-shrink-0" style="background-color:#1a2f4a; color:white;">
                <h2 class="text-lg font-semibold" style="font-family:'Playfair Display',serif;">Book Details</h2>
                <button onclick="closeModal('bookDetailModal')" class="text-white/70 hover:text-white text-xl">&times;</button>
            </div>

            <!-- Body -->
            <div class="flex overflow-hidden flex-1">
                <!-- Cover -->
                <div id="detailCoverWrap" class="w-40 flex-shrink-0 flex items-center justify-center" style="background-color:#1a2f4a;">
                    <img id="detailCoverImg" src="" alt="" class="w-full h-full object-cover hidden">
                    <i id="detailCoverIcon" class="fas fa-file-alt text-white text-5xl opacity-60"></i>
                </div>

                <!-- Info -->
                <div class="flex-1 p-6 overflow-y-auto space-y-4">
                    <div>
                        <h3 id="detailTitle" class="text-2xl font-bold leading-tight" style="color:#1a2f4a; font-family:'Playfair Display',serif;"></h3>
                        <p id="detailAuthor" class="text-slate-500 text-sm mt-1"></p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span id="detailCategory" class="text-xs font-semibold px-3 py-1 rounded-full" style="background-color:#d4edda; color:#155724;"></span>
                        <span id="detailPages" class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600"></span>
                    </div>

                    <p id="detailDescription" class="text-slate-600 text-sm leading-relaxed"></p>

                    <div class="text-xs text-slate-400 space-y-1">
                        <p><span class="font-semibold text-slate-500">Upload Date:</span> <span id="detailDate"></span></p>
                    </div>

                    <a id="detailReadLink" href="#" target="_blank"
                        class="inline-flex items-center gap-2 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition"
                        style="background-color:#1a2f4a;"
                        onmouseover="this.style.backgroundColor='#132238'"
                        onmouseout="this.style.backgroundColor='#1a2f4a'">
                        <i class="fas fa-book-open text-xs"></i> Read Book
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        // Add Book
        document.getElementById('addBookForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const fd = new FormData(this);
            const btn = this.querySelector('[type=submit]');
            btn.textContent = 'Uploading…';
            btn.disabled = true;
            try {
                const res = await fetch('/library/books', {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json();
                if (res.ok) {
                    closeModal('addBookModal');
                    this.reset();
                    location.reload();
                } else {
                    alert(data.error || 'Error uploading book');
                }
            } catch (err) {
                alert('Network error');
            }
            btn.textContent = 'Upload Book';
            btn.disabled = false;
        });

        // Edit Book
        document.getElementById('editBookForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('editBookId').value;

            if (!id || isNaN(id)) {
                alert('Book ID is missing or invalid');
                return;
            }

            const btn = this.querySelector('[type=submit]');
            btn.textContent = 'Saving…';
            btn.disabled = true;

            try {
                const coverFile = document.getElementById('editCover').files[0];

                let res;

                if (coverFile) {
                    // Si hay imagen, usar FormData con POST + _method override
                    // O hacer dos requests: primero texto, luego imagen
                    const fd = new FormData();
                    fd.append('title', document.getElementById('editTitle').value);
                    fd.append('author', document.getElementById('editAuthor').value);
                    fd.append('description', document.getElementById('editDescription').value);
                    fd.append('pages', parseInt(document.getElementById('editPages').value) || 0);
                    fd.append('category_id', parseInt(document.getElementById('editCategory').value) || 0);
                    fd.append('cover', coverFile);

                    // Usar POST con parámetro _method=PUT en la URL
                    res = await fetch('/library/books/' + id + '?_method=PUT', {
                        method: 'POST',
                        body: fd
                    });
                } else {
                    // Sin imagen: enviar JSON normal
                    res = await fetch('/library/books/' + id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            title: document.getElementById('editTitle').value,
                            author: document.getElementById('editAuthor').value,
                            description: document.getElementById('editDescription').value,
                            pages: parseInt(document.getElementById('editPages').value) || 0,
                            category_id: parseInt(document.getElementById('editCategory').value) || 0,
                        })
                    });
                }

                if (res.ok) {
                    closeModal('editBookModal');
                    location.reload();
                } else {
                    const data = await res.json();
                    alert(data.error || 'Error updating book');
                }
            } catch (err) {
                alert('Network error: ' + err.message);
            }

            btn.textContent = 'Save Changes';
            btn.disabled = false;
        });

        async function openEditModal(bookId) {
            const res = await fetch('/library/books/' + bookId, {});
            const book = await res.json();
            document.getElementById('editBookId').value = book.id;
            document.getElementById('editTitle').value = book.title;
            document.getElementById('editAuthor').value = book.author;
            document.getElementById('editDescription').value = book.description || '';
            document.getElementById('editPages').value = book.pages || 0;

            // Populate categories
            const catRes = await fetch('/library/categories');
            const cats = await catRes.json();
            const sel = document.getElementById('editCategory');
            sel.innerHTML = '<option value="">Select category</option>';
            cats.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                if (c.id === book.category_id) opt.selected = true;
                sel.appendChild(opt);
            });

            openModal('editBookModal');
        }

        async function deleteBook(id, title) {
            if (!confirm('Delete "' + title + '"? This cannot be undone.')) return;
            const res = await fetch('/library/books/' + id, {
                method: 'DELETE',
            });
            if (res.ok) location.reload();
            else alert('Error deleting book');
        }

        async function openBookDetail(bookId) {
            const res = await fetch('/library/books/' + bookId);
            if (!res.ok) {
                alert('Could not load book details');
                return;
            }
            const book = await res.json();

            document.getElementById('detailTitle').textContent = book.title;
            document.getElementById('detailAuthor').textContent = 'by ' + book.author;
            document.getElementById('detailCategory').textContent = book.categoryName || '—';
            document.getElementById('detailPages').textContent = (book.pages || 0) + ' pages';
            document.getElementById('detailDescription').textContent = book.description || 'No description available.';
            document.getElementById('detailDate').textContent = book.uploadedAt ? new Date(book.uploadedAt).toLocaleDateString() : '—';
            document.getElementById('detailReadLink').href = 'assets/pdfs/' + book.pdfPath;

            const img = document.getElementById('detailCoverImg');
            const icon = document.getElementById('detailCoverIcon');
            if (book.cover && book.cover !== 'default.jpg') {
                img.src = 'assets/covers/' + book.cover;
                img.classList.remove('hidden');
                icon.classList.add('hidden');
            } else {
                img.classList.add('hidden');
                icon.classList.remove('hidden');
            }

            openModal('bookDetailModal');
        }

        // Search & Filter
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');

        function applyFilters() {
            const q = searchInput ? searchInput.value.toLowerCase() : '';
            const cat = categoryFilter ? categoryFilter.value : '';
            document.querySelectorAll('.book-card').forEach(card => {
                const titleMatch = card.dataset.title.toLowerCase().includes(q);
                const authorMatch = card.dataset.author.toLowerCase().includes(q);
                const catMatch = !cat || card.dataset.category === cat;
                card.style.display = (titleMatch || authorMatch) && catMatch ? '' : 'none';
            });
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (categoryFilter) categoryFilter.addEventListener('change', applyFilters);

        // Close modals on backdrop click
        ['addBookModal', 'editBookModal'].forEach(id => {
            document.getElementById(id).addEventListener('click', function(e) {
                if (e.target === this) closeModal(id);
            });
        });
    </script>
    </body>

    </html>