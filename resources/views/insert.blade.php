<?php
// Set the content type to UTF-8 to handle all characters correctly
header('Content-Type: text/html; charset=utf-8');
// Use the correct path for the service file
// NOTE: This file assumes mysql_service.php exists in the same directory for database connection.
// require_once __DIR__ . '/mysql_service.php'; 

// Initialize variables (mocking connection for demonstration)
$db = null; // Assume connection might fail for initial rendering
$latest_report_data = null;
$message = null;

// Mock database functions for demonstration purposes where the backend is not active
function connect_db() { return false; } // Mocking a failed connection
function get_latest_report($db) { 
    // Mock data for display purposes
    return [
        'name' => 'John Doe',
        'student_id' => 'S123456',
        'grade_level' => '10th Grade / Secondary School',
        'term' => 'Academic Year 2024-2025',
        'date_generated' => date('Y-m-d'),
        'photo_base64' => '',
        'results_json' => '[
            {"subject":"Mathematics","ca":25,"test":18,"exam":49,"total":92,"percentage":92.00,"grade":"A"},
            {"subject":"Physics","ca":22,"test":17,"exam":49,"total":88,"percentage":88.00,"grade":"B"},
            {"subject":"English Language","ca":28,"test":20,"exam":47,"total":95,"percentage":95.00,"grade":"A"},
            {"subject":"Chemistry","ca":15,"test":10,"exam":30,"total":55,"percentage":55.00,"grade":"F"},
            {"subject":"Biology","ca":20,"test":15,"exam":38,"total":73,"percentage":73.00,"grade":"C"},
            {"subject":"Economics","ca":29,"test":19,"exam":50,"total":98,"percentage":98.00,"grade":"A"}
        ]'
    ];
}

function getLetterGrade($percentage) {
    if ($percentage >= 90) return 'A';
    if ($percentage >= 80) return 'B';
    if ($percentage >= 70) return 'C';
    if ($percentage >= 60) return 'D';
    return 'F';
}

function calculate_results($raw_report) {
    $student_data = [
        'name' => $raw_report['name'],
        'student_id' => $raw_report['student_id'],
        'grade_level' => $raw_report['grade_level'],
        'term' => $raw_report['term'],
        'date_generated' => $raw_report['date_generated'],
        'photo_base64' => $raw_report['photo_base64'] ?? ''
    ];
    $results = json_decode($raw_report['results_json'], true);

    $total_subject_percentages = 0;
    $processed_results = [];
    foreach ($results as $item) {
        $total = $item['ca'] + $item['test'] + $item['exam'];
        $percentage = $total; 
        $grade = getLetterGrade($percentage);
        
        $item['total'] = $total;
        $item['percentage'] = number_format($percentage, 2);
        $item['grade'] = $grade;
        $processed_results[] = $item;
        $total_subject_percentages += $percentage;
    }
    
    $total_subjects = count($processed_results);
    $overall_average_percentage = $total_subjects > 0 ? $total_subject_percentages / $total_subjects : 0;
    $status = $overall_average_percentage >= 65 ? 'PROMOTED' : 'REFERRED';
    $status_color = $overall_average_percentage >= 65 ? 'text-indigo-600' : 'text-red-600';

    return [
        'studentData' => $student_data,
        'overallPercentage' => number_format($overall_average_percentage, 2),
        'totalSubjects' => $total_subjects,
        'status' => $status,
        'statusColor' => $status_color,
        'processedResults' => $processed_results
    ];
}


// ===================================================================
//  SINGLE SOURCE OF TRUTH FOR SUBJECTS
// ===================================================================
$subjects_list = [
    'Mathematics', 
    'Physics', 
    'English Language', 
    'Chemistry', 
    'Biology', 
    'Economics'
];
// ===================================================================

// --- Handle Form Submission (Mock) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mock successful submission for a real form post
    $message = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4' role='alert'><strong>Success!</strong> Report for submitted successfully. (Mocked database save)</div>";
}

// --- Fetch Latest Report Data for Display ---
// In a real scenario, $db would be connected and the report fetched here.
$raw_report = get_latest_report($db);
if ($raw_report) {
    $latest_report_data = calculate_results($raw_report);
}


// --- Report Generation Helper Functions (Used by the display logic) ---

/**
 * Renders an alert message if the database connection failed.
 */
function render_db_error() {
    echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4' role='alert'>
        <strong>FATAL Error:</strong> Could not connect to MySQL Database.
        <p>Please ensure your XAMPP/WAMP MySQL service is running and check the credentials in <strong>mysql_service.php</strong>.</p>
    </div>";
}

/**
 * Displays the main student report structure.
 * @param array $data The processed report data.
 */
function render_report($data) {
    if (!$data) {
        echo '<div class="text-center py-12 text-gray-500">No report data found. Submit the form above to generate the first report.</div>';
        return;
    }

    $student = $data['studentData'];
    $results = $data['processedResults'];
    $overall_percentage = $data['overallPercentage'];
    $status = $data['status'];
    $status_color = $data['statusColor'];

    // Determine the source for the student photo
    $photo_base64 = $student['photo_base64'] ?? '';
    $photo_src = !empty($photo_base64) ? 'data:image/png;base64,' . htmlspecialchars($photo_base64) : 'https://placehold.co/96x96/e5e7eb/4b5563?text=STUDENT'; // Use a proper placeholder image

    // ----------------------------------------------------
    // --- START OF PRINTABLE REPORT CONTENT ---
    // ----------------------------------------------------
    echo '
    <div id="report-content" class="bg-white shadow-xl rounded-lg overflow-hidden my-6 p-4 md:p-6 print:p-0 print:m-0">
        <div class="print:shadow-none print:border-none print:min-h-[297mm] print:w-[210mm] print:mx-auto">
            
            <div class="text-center py-4 px-4 sm:px-6 bg-gray-50 print:bg-white print:border-b-2 border-gray-300">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-indigo-700 uppercase tracking-wider print:text-xl">
                    [Secondary School Name Placeholder]
                </h1>
                <p class="text-gray-600 text-xs sm:text-sm print:text-xs">Official Academic Report - ' . $student['term'] . ' Term</p>
            </div>

            <div class="p-4 sm:p-6 border-b border-gray-200 bg-white print:px-8 print:py-4">
                <div class="flex flex-col sm:flex-row items-center sm:items-start"> 
                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 mb-4 sm:mb-0 sm:mr-6 flex-shrink-0 print:w-16 print:h-16 print:text-xs overflow-hidden">
                        ' . ($photo_src !== 'placeholder' ? 
                            '<img src="' . $photo_src . '" alt="Student Photo" class="object-cover w-full h-full rounded-lg">' : 
                            '<img src="https://placehold.co/80x80/e5e7eb/4b5563?text=STUDENT" class="w-full h-full rounded-lg">'
                        ) . '
                    </div>
                    
                    <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-6 text-sm print:text-xs print:gap-x-4 w-full">
                        <div class="col-span-1 border-b sm:border-b-0 pb-1 sm:pb-0">
                            <span class="font-semibold text-gray-600 text-xs">Student Name:</span> 
                            <span class="font-bold text-gray-900 block">' . htmlspecialchars($student['name']) . '</span>
                        </div>
                        <div class="col-span-1 border-b sm:border-b-0 pb-1 sm:pb-0">
                            <span class="font-semibold text-gray-600 text-xs">Student ID:</span> 
                            <span class="font-bold text-gray-900 block">' . htmlspecialchars($student['student_id']) . '</span>
                        </div>
                        <div class="col-span-1 border-b sm:border-b-0 pb-1 sm:pb-0">
                            <span class="font-semibold text-gray-600 text-xs">Class/Grade:</span> 
                            <span class="font-bold text-gray-900 block">' . htmlspecialchars($student['grade_level']) . '</span>
                        </div>
                        <div class="col-span-1 border-b sm:border-b-0 pb-1 sm:pb-0">
                            <span class="font-semibold text-gray-600 text-xs">Date Generated:</span> 
                            <span class="font-bold text-gray-900 block">' . date('F j, Y', strtotime($student['date_generated'])) . '</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50 grid grid-cols-3 gap-2 sm:gap-4 print:px-8 print:py-3 print:bg-white print:border-b-2">
                
                <div class="text-center p-2 sm:p-3 rounded-lg shadow-sm bg-white print:border print:border-gray-300">
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase print:text-[10px]">Total Subjects</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-indigo-700 print:text-xl">' . $data['totalSubjects'] . '</p>
                </div>

                <div class="text-center p-2 sm:p-3 rounded-lg shadow-sm bg-white print:border print:border-gray-300">
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase print:text-[10px]">Overall Avg (%)</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-indigo-700 print:text-xl">' . $overall_percentage . '%</p>
                </div>

                <div class="text-center p-2 sm:p-3 rounded-lg shadow-sm bg-white print:border print:border-gray-300">
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase print:text-[10px]">Status</p>
                    <p class="text-xl sm:text-2xl font-extrabold ' . $status_color . ' print:text-xl">' . $status . '</p>
                </div>
            </div>

            <div class="p-4 sm:p-6 overflow-x-auto print:px-8 print:py-4">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 print:text-base print:mb-2">Subject Performance Details</h2>
                <table class="min-w-full sm:min-w-[500px] divide-y divide-gray-200 text-sm print:text-[10px] print:leading-tight">
                    <thead class="bg-gray-50 print:bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left font-bold text-gray-600 uppercase tracking-wider w-3/12">Subject</th>
                            <th class="hidden sm:table-cell px-2 py-2 text-center font-bold text-gray-600 uppercase">CA (30)</th>
                            <th class="hidden sm:table-cell px-2 py-2 text-center font-bold text-gray-600 uppercase">Test (20)</th>
                            <th class="hidden sm:table-cell px-2 py-2 text-center font-bold text-gray-600 uppercase">Exam (50)</th>
                            <th class="px-2 py-2 text-center font-bold text-gray-600 uppercase">Total (100)</th>
                            <th class="px-2 py-2 text-center font-bold text-gray-600 uppercase">Percent (%)</th>
                            <th class="px-2 py-2 text-center font-bold text-gray-600 uppercase">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ';
                        foreach ($results as $item) {
                            $grade_class = ($item['grade'] == 'F') ? 'text-red-600' : 'text-indigo-600';
                            echo '<tr class="hover:bg-gray-50">';
                            echo '<td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900">';
                            echo htmlspecialchars($item['subject']);
                            // Mobile-only display of component scores below the subject name
                            echo '<div class="sm:hidden text-[10px] text-gray-500 font-normal mt-1 leading-none">';
                            echo 'CA: ' . htmlspecialchars($item['ca']) . ', Test: ' . htmlspecialchars($item['test']) . ', Exam: ' . htmlspecialchars($item['exam']);
                            echo '</div>';
                            echo '</td>';
                            // Component Score Cells (Hidden on small screens)
                            echo '<td class="hidden sm:table-cell px-2 py-2 whitespace-nowrap text-center">' . htmlspecialchars($item['ca']) . '</td>';
                            echo '<td class="hidden sm:table-cell px-2 py-2 whitespace-nowrap text-center">' . htmlspecialchars($item['test']) . '</td>';
                            echo '<td class="hidden sm:table-cell px-2 py-2 whitespace-nowrap text-center">' . htmlspecialchars($item['exam']) . '</td>';
                            echo '<td class="px-2 py-2 whitespace-nowrap text-center font-bold">' . htmlspecialchars($item['total']) . '</td>';
                            echo '<td class="px-2 py-2 whitespace-nowrap text-center font-bold ' . $grade_class . '">' . htmlspecialchars($item['percentage']) . '</td>';
                            echo '<td class="px-2 py-2 whitespace-nowrap text-center font-extrabold ' . $grade_class . '">' . htmlspecialchars($item['grade']) . '</td>';
                            echo '</tr>';
                        }
                        echo '
                    </tbody>
                </table>
            </div>

            <div class="p-4 sm:p-6 bg-gray-50 print:px-8 print:py-4 print:bg-white print:border-t-2 border-gray-300">
                <h3 class="text-sm font-semibold text-gray-700 mb-2 print:text-xs">Grading Scale Legend:</h3>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 text-xs print:text-[10px]">
                    <span class="font-medium text-gray-800">A: 90% - 100%</span>
                    <span class="font-medium text-gray-800">B: 80% - 89%</span>
                    <span class="font-medium text-gray-800">C: 70% - 79%</span>
                    <span class="font-medium text-gray-800">D: 60% - 69%</span>
                    <span class="font-medium text-gray-800">F: Below 60%</span>
                </div>
            </div>
            
            <div class="text-center py-2 text-gray-500 text-xs italic print:text-[8px] print:py-2">
                This report is computer generated and does not require a signature.
            </div>

        </div>
    </div>
    ';
}

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .container {
            max-width: 1000px;
        }
        /* Adjusted padding on the main container for small screens */
        .container.mx-auto.p-4.md\:p-8 {
            padding: 1rem; /* p-4 */
        }

        /* PRINT STYLES: Optimize for A4 single page printout */
        @media print {
            body {
                background: none !important;
                color: #000;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            /* A4 size optimization */
            #report-content {
                width: 210mm; /* A4 width */
                min-height: 297mm; /* A4 height */
                margin: 0 auto !important;
                box-shadow: none !important;
                border: none !important;
                padding: 0;
                font-size: 11px; /* Slightly reduce font size for compression */
            }
            .py-6, .p-6, .px-6 {
                padding: 0.4in !important; /* Standard print margin */
            }
            table th, table td {
                padding: 3px 5px !important;
            }
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4 md:p-8">
        
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 text-center no-print">Academic Data Entry Portal (MySQL)</h1>

        <?php 
        // Display any success or error messages from the insertion process
        if ($message) {
            echo $message;
        }
        
        // If DB connection failed, display a general error and stop
        if (!$db) {
            render_db_error();
        }
        ?>

        <div class="no-print bg-white p-4 sm:p-6 shadow-xl rounded-lg mb-8 border-t-4 border-indigo-500">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Input New Student Results</h2>
            <p class="text-xs sm:text-sm text-gray-600 mb-6">Enter scores for CA (30%), Test (20%), and Exam (50%) for each subject.</p>

            <form method="POST" action="insert.php" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6"> 
                    <div class="col-span-2 sm:col-span-1">
                        <label for="student_name" class="block text-sm font-medium text-gray-700">Student Name</label>
                        <input type="text" name="student_name" id="student_name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-sm">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                        <input type="text" name="student_id" id="student_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-sm">
                    </div>
                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700">Grade/Class</label>
                        <input type="text" name="grade_level" id="grade_level" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-sm" value="SS2 A">
                    </div>
                    <div>
                        <label for="term" class="block text-sm font-medium text-gray-700">Academic Term</label>
                        <select name="term" id="term" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border text-sm">
                            <option value="First">First Term</option>
                            <option value="Second">Second Term</option>
                            <option value="Third" selected>Third Term</option>
                        </select>
                    </div>
                    <div class="col-span-2 sm:col-span-4 border-t pt-4">
                        <label for="student_photo" class="block text-sm font-medium text-gray-700">Upload Student Photo (Max 2MB)</label>
                        <input type="file" name="student_photo" id="student_photo" accept="image/*" class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 pt-4 border-t mt-4">Subject Scores</h3>
                    
                    <?php
                    // -------------------------------------------------------------------
                    // 2. HTML FORM GENERATION: Using the single source of truth: $subjects_list
                    // -------------------------------------------------------------------
                    foreach ($subjects_list as $name) {
                        // Generate the clean key dynamically, as done in the submission logic
                        $key = strtolower(str_replace(' ', '_', $name)); 
                        
                        // Use grid-cols-[3fr,1fr,1fr,1fr] on mobile for better subject name visibility
                        echo '<div class="grid grid-cols-4 gap-2 sm:gap-4 items-center bg-gray-50 p-3 rounded-md border border-gray-200">';
                        echo '<div class="col-span-1 text-sm font-medium text-gray-700">' . $name . '</div>';
                        
                        // CA
                        echo '<div><label class="block text-xs text-gray-500 text-center">CA (30)</label><input type="number" name="' . $key . '_ca" min="0" max="30" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-1 border text-sm text-center" value="25"></div>';
                        
                        // Test
                        echo '<div><label class="block text-xs text-gray-500 text-center">Test (20)</label><input type="number" name="' . $key . '_test" min="0" max="20" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-1 border text-sm text-center" value="15"></div>';
                        
                        // Exam
                        echo '<div><label class="block text-xs text-gray-500 text-center">Exam (50)</label><input type="number" name="' . $key . '_exam" min="0" max="50" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-1 border text-sm text-center" value="45"></div>';
                        
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between items-center pt-2 space-y-3 sm:space-y-0">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 sm:py-3 sm:px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out w-full sm:w-auto">
                        Save to MySQL DB & Generate Report
                    </button>
                    <div class="text-xs sm:text-sm text-gray-500 font-semibold">
                        Database: MySQL (school_reports)
                    </div>
                </div>
            </form>
        </div>

        <?php
        // Only render the report if the database connection was successful (or mock data exists)
        if ($db || $latest_report_data) {
            render_report($latest_report_data);
        ?>
            <div class="flex justify-center no-print mt-6">
                <button onclick="window.print()" class="flex items-center space-x-2 py-2 px-6 sm:py-3 sm:px-8 border border-transparent shadow-lg text-sm font-medium rounded-full text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <span>Download PDF</span>
                </button>
            </div>
        <?php
        }
        ?>

    </div>
    <script>
        // Initialize lucide icons on load (for the download button)
        document.addEventListener('DOMContentLoaded', (event) => {
            lucide.createIcons();
        });
    </script>
</body>
</html>