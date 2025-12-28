<?php
require_once 'includes/auth_check.php';

// Récupérer les catégories
$query = "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.category_id) as product_count 
          FROM categories c ORDER BY c.sort_order ASC, c.name ASC";
$categories_result = mysqli_query($cnx, $query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Catégories - Admin Nuraya</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-common.css">
    <script src="../assets/js/toast.js"></script>
    <style>
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        @media (max-width: 600px) {
            .categories-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .category-card {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(200, 182, 166, 0.1);
            border: 1px solid rgba(200, 182, 166, 0.1);
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(200, 182, 166, 0.2);
            border-color: var(--beige-dark);
        }
        
        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .category-header h3 { font-size: 20px; font-weight: 700; color: var(--text-dark); }
        
        .category-info {
            color: var(--text-gray);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .category-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid rgba(200, 182, 166, 0.1);
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="page-header">
                <h1>Gestion des Catégories</h1>
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle Catégorie
                </button>
            </div>
            
            <div class="categories-grid">
                <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                    <div class="category-card">
                        <div class="category-header">
                            <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                            <span class="badge <?php echo $cat['is_active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                <?php echo $cat['is_active'] ? 'Actif' : 'Inactif'; ?>
                            </span>
                        </div>
                        <div class="category-info">
                            <p><?php echo htmlspecialchars($cat['description'] ?: 'Aucune description'); ?></p>
                            <small style="display: block; margin-top: 10px;">Slug: <strong><?php echo htmlspecialchars($cat['slug']); ?></strong></small>
                            <small>Ordre: <?php echo $cat['sort_order']; ?></small>
                        </div>
                        <div class="category-footer">
                            <div style="font-size: 13px; font-weight: 600; color: var(--beige-dark);">
                                <i class="fas fa-box"></i> <?php echo $cat['product_count']; ?> produits
                            </div>
                            <div class="actions">
                                <button onclick="openModal(<?php echo htmlspecialchars(json_encode($cat)); ?>)" 
                                        class="btn-icon btn-edit" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteCategory(<?php echo $cat['category_id']; ?>)" 
                                        class="btn-icon btn-delete" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="card-header">
                <h2 id="modalTitle">Nouvelle Catégorie</h2>
            </div>
            <form id="categoryForm">
                <input type="hidden" name="category_id" id="cat_id">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="name" id="cat_name" required onkeyup="updateSlug(this.value)">
                </div>
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" id="cat_slug" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="cat_description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Statut</label>
                    <select name="is_active" id="cat_active">
                        <option value="1">Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ordre de tri</label>
                    <input type="number" name="sort_order" id="cat_sort" value="0">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        const modal = document.getElementById('categoryModal');
        const form = document.getElementById('categoryForm');

        function openModal(data = null) {
            if (data) {
                document.getElementById('modalTitle').textContent = 'Modifier la Catégorie';
                document.getElementById('cat_id').value = data.category_id;
                document.getElementById('cat_name').value = data.name;
                document.getElementById('cat_slug').value = data.slug;
                document.getElementById('cat_description').value = data.description;
                document.getElementById('cat_active').value = data.is_active;
                document.getElementById('cat_sort').value = data.sort_order;
            } else {
                document.getElementById('modalTitle').textContent = 'Nouvelle Catégorie';
                form.reset();
                document.getElementById('cat_id').value = '';
            }
            modal.classList.add('show');
        }

        function closeModal() {
            modal.classList.remove('show');
        }

        function updateSlug(name) {
            if (!document.getElementById('cat_id').value) {
                document.getElementById('cat_slug').value = name.toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w-]+/g, '');
            }
        }

        form.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('action', 'save');
            
            fetch('api/categories.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showToast(data.message, 'error');
                }
            });
        };

        function deleteCategory(id) {
            if (!confirm('Voulez-vous vraiment supprimer cette catégorie ?')) return;
            
            fetch('api/categories.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&category_id=${id}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showToast(data.message, 'error');
                }
            });
        }

        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>
</body>
</html>
