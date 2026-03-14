<?php

declare(strict_types=1);

final class ProductService
{
    public function __construct(private mysqli $conn)
    {
    }

    public function findImageById(int $id): ?string
    {
        $statement = mysqli_prepare($this->conn, 'SELECT image FROM products WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($statement, 'i', $id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $product = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($statement);

        return $product['image'] ?? null;
    }
}
