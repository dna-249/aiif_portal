<?php
// --- MySQL Configuration ---
const DB_HOST = 'localhost';
const DB_USER = 'root'; // Default XAMPP username
const DB_PASS = '';     // Default XAMPP password (often empty)
const DB_NAME = 'school_reports'; // IMPORTANT: Create this database in PHPMyAdmin first!

/**
 * Connects to the MySQL database. If the table does not exist, it initializes the schema.
 * @return mysqli|false A mysqli object on success, or false on failure.
 */
function connect_db() {
    // Attempt to connect to MySQL server
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        // Log the detailed connection error
        error_log("MySQL Connection Error: " . $conn->connect_error);
        return false;
    }

    // Check if the reports table exists. If not, create it.
    $check_table_sql = "SHOW TABLES LIKE 'reports'";
    $result = $conn->query($check_table_sql);

    if ($result && $result->num_rows === 0) {
        initialize_db($conn);
    }
    
    return $conn;
}

/**
 * Creates the necessary table structure for the student reports.
 * @param mysqli $conn The active database connection object.
 */
function initialize_db(mysqli $conn) {
    // Schema definition for the reports table using MySQL syntax
    $sql = "CREATE TABLE IF NOT EXISTS reports (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        student_id VARCHAR(50) NOT NULL,
        grade_level VARCHAR(100),
        term VARCHAR(100),
        date_generated VARCHAR(50),
        results_json TEXT,
        timestamp INT(11)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (!$conn->query($sql)) {
        error_log("MySQL Table Creation Error: " . $conn->error);
    }
}

/**
 * Fetches the most recently saved student report from the database.
 * @param mysqli $conn The active database connection object.
 * @return array|null The associative array of the latest report, or null if none found.
 */
function get_latest_report(mysqli $conn) {
    // Get the latest report based on the highest timestamp
    $sql = "SELECT * FROM reports ORDER BY timestamp DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        // Use fetch_assoc() for associative array fetching
        return $result->fetch_assoc();
    }
    return null;
}

/**
 * Calculates total scores, percentages, and letter grades for a raw report.
 * This is the main business logic for report generation.
 * @param array $report The raw report data fetched from the DB, including 'results_json'.
 * @return array The processed report data ready for display.
 */
function calculate_results(array $report): array {
    // The calculation logic remains the same regardless of the underlying database (MySQL or SQLite)
    $results = json_decode($report['results_json'], true);
    if (!is_array($results)) {
        return ['studentData' => $report, 'processedResults' => [], 'overallPercentage' => 0.00, 'status' => 'No Data', 'statusColor' => 'text-gray-500', 'totalSubjects' => 0];
    }

    $total_score_sum = 0;
    $total_max_score_sum = 0;
    $processed_results = [];

    foreach ($results as $item) {
        // Calculate total score for the subject (Max 100: CA 30 + Test 20 + Exam 50)
        $total = ($item['ca'] ?? 0) + ($item['test'] ?? 0) + ($item['exam'] ?? 0);
        $percentage = ($total / 100.0) * 100;
        
        // Determine letter grade
        if ($percentage >= 90) {
            $grade = 'A';
        } elseif ($percentage >= 80) {
            $grade = 'B';
        } elseif ($percentage >= 70) {
            $grade = 'C';
        } elseif ($percentage >= 60) {
            $grade = 'D';
        } else {
            $grade = 'F';
        }

        $processed_results[] = array_merge($item, [
            'total' => $total,
            'percentage' => number_format($percentage, 2),
            'grade' => $grade,
        ]);

        $total_score_sum += $total;
        $total_max_score_sum += 100; // Each subject is max 100
    }

    $total_subjects = count($processed_results);
    
    // Calculate overall average percentage
    $overall_percentage = $total_subjects > 0 ? ($total_score_sum / $total_max_score_sum) * 100 : 0;
    $overall_percentage_formatted = number_format($overall_percentage, 2);

    // Determine overall academic status (e.g., Pass/Fail)
    $status = 'Fail';
    $status_color = 'text-red-600';
    if ($overall_percentage >= 90) {
        $status = 'Excellent';
        $status_color = 'text-green-600';
    } elseif ($overall_percentage >= 65) {
        $status = 'Pass';
        $status_color = 'text-indigo-600';
    }

    return [
        'studentData' => $report,
        'processedResults' => $processed_results,
        'overallPercentage' => $overall_percentage_formatted,
        'status' => $status,
        'statusColor' => $status_color,
        'totalSubjects' => $total_subjects,
    ];
}
