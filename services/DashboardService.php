<?php

declare(strict_types=1);

final class DashboardService
{
    public function __construct(private mysqli $conn)
    {
    }

    public function getStats(): array
    {
        $stats = [
            'products' => 0,
            'categories' => 0,
            'average_price' => 0,
            'latest_products' => [],
            'category_breakdown' => [],
        ];

        $totalsResult = mysqli_query(
            $this->conn,
            'SELECT
                (SELECT COUNT(*) FROM products) AS products,
                (SELECT COUNT(*) FROM categories) AS categories,
                (SELECT COALESCE(AVG(price), 0) FROM products) AS average_price'
        );

        if ($totals = mysqli_fetch_assoc($totalsResult)) {
            $stats['products'] = (int) $totals['products'];
            $stats['categories'] = (int) $totals['categories'];
            $stats['average_price'] = (float) $totals['average_price'];
        }

        $latestResult = mysqli_query(
            $this->conn,
            'SELECT p.id, p.name, p.price, p.created_at, c.name AS category_name
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             ORDER BY p.id DESC
             LIMIT 5'
        );

        while ($row = mysqli_fetch_assoc($latestResult)) {
            $stats['latest_products'][] = $row;
        }

        $breakdownResult = mysqli_query(
            $this->conn,
            'SELECT c.name, COUNT(p.id) AS total
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id, c.name
             ORDER BY total DESC, c.name ASC'
        );

        while ($row = mysqli_fetch_assoc($breakdownResult)) {
            $stats['category_breakdown'][] = $row;
        }

        return $stats;
    }
}
