<?php

declare(strict_types=1);

final class CategoryService
{
    public function __construct(private mysqli $conn)
    {
    }

    public function allWithProductCount(): array
    {
        $categories = [];
        $sql = 'SELECT c.id, c.name, COUNT(p.id) AS products_count
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id
                GROUP BY c.id, c.name
                ORDER BY c.name ASC';
        $result = mysqli_query($this->conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }

        return $categories;
    }

    public function all(): array
    {
        $categories = [];
        $result = mysqli_query($this->conn, 'SELECT id, name FROM categories ORDER BY name ASC');

        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }

        return $categories;
    }

    public function findById(int $id): ?array
    {
        $statement = mysqli_prepare($this->conn, 'SELECT id, name FROM categories WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($statement, 'i', $id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $category = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($statement);

        return $category;
    }

    public function exists(int $id): bool
    {
        return $this->findById($id) !== null;
    }

    public function nameExists(string $name, ?int $ignoreId = null): bool
    {
        if ($ignoreId) {
            $statement = mysqli_prepare($this->conn, 'SELECT id FROM categories WHERE name = ? AND id != ? LIMIT 1');
            mysqli_stmt_bind_param($statement, 'si', $name, $ignoreId);
        } else {
            $statement = mysqli_prepare($this->conn, 'SELECT id FROM categories WHERE name = ? LIMIT 1');
            mysqli_stmt_bind_param($statement, 's', $name);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $exists = (bool) mysqli_fetch_assoc($result);
        mysqli_stmt_close($statement);

        return $exists;
    }

    public function create(string $name): void
    {
        $statement = mysqli_prepare($this->conn, 'INSERT INTO categories (name) VALUES (?)');
        mysqli_stmt_bind_param($statement, 's', $name);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }

    public function update(int $id, string $name): void
    {
        $statement = mysqli_prepare($this->conn, 'UPDATE categories SET name = ? WHERE id = ?');
        mysqli_stmt_bind_param($statement, 'si', $name, $id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }

    public function productsCount(int $id): int
    {
        $statement = mysqli_prepare($this->conn, 'SELECT COUNT(*) AS total FROM products WHERE category_id = ?');
        mysqli_stmt_bind_param($statement, 'i', $id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $count = (int) (mysqli_fetch_assoc($result)['total'] ?? 0);
        mysqli_stmt_close($statement);

        return $count;
    }

    public function delete(int $id): void
    {
        $statement = mysqli_prepare($this->conn, 'DELETE FROM categories WHERE id = ?');
        mysqli_stmt_bind_param($statement, 'i', $id);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
}
