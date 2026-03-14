<?php

declare(strict_types=1);

final class AuthService
{
    public function __construct(private mysqli $conn)
    {
    }

    public function findAdminByEmail(string $email): ?array
    {
        $statement = mysqli_prepare($this->conn, 'SELECT id, name, email, password FROM admins WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($statement, 's', $email);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $admin = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($statement);

        return $admin;
    }

    public function changePassword(int $adminId, string $newPassword): void
    {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $statement = mysqli_prepare($this->conn, 'UPDATE admins SET password = ? WHERE id = ?');
        mysqli_stmt_bind_param($statement, 'si', $passwordHash, $adminId);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }

    public function verifyCurrentPassword(int $adminId, string $password): bool
    {
        $statement = mysqli_prepare($this->conn, 'SELECT password FROM admins WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($statement, 'i', $adminId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $admin = mysqli_fetch_assoc($result);
        mysqli_stmt_close($statement);

        if (!$admin) {
            return false;
        }

        return password_verify($password, (string) $admin['password']);
    }
}
