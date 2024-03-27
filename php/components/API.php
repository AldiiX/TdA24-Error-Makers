<?php

class Api {
    public static function activitiesGET(): array {
        $conn = \Functions\Database::getDatabase();
        $stmt = $conn->prepare("SELECT * FROM activities");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}