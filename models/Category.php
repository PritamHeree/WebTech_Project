<?php
class Category {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // categories are simple lookup/update records tied to the same DB connection.
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($name) {
        // add a new category for menu items
        $stmt = $this->pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }
    
    public function update($id, $name) {
        // rename an existing category
        $stmt = $this->pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }
    
    public function delete($id) {
        // prevent deleting categories that are still in use
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as cnt FROM menu_items WHERE category_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row['cnt'] > 0) {
            return false; // Cannot delete
        }
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getCount() {
        return $this->pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    }
}
