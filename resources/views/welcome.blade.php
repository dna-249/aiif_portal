<?php
// PHP Backend Logic for Student Result Data and Calculations

// --- 1. Sample Student and Result Data ---
$student_data = [
    'name' => 'Alex Johnson',
    'student_id' => 'S1234567',
    'grade_level' => '10th Grade / Secondary School', 
    'term' => 'Academic Year 2024-2025', 
    'date_generated' => date('F d, Y'),
];

// Subject results. Scores are out of an assumed maximum for each component (e.g., CA=30, Test=20, Exam=50. Total Max = 100)
$results = [
    ['subject' => 'Mathematics', 'ca' => 25, 'test' => 18, 'exam' => 49], // Total: 92 (A)
    ['subject' => 'Physics', 'ca' => 22, 'test' => 17, 'exam' => 49], // Total: 88 (B)
    ['subject' => 'English Language', 'ca' => 28, 'test' => 20, 'exam' => 47], // Total: 95 (A)
    ['subject' => 'Computer Studies', 'ca' => 15, 'test' => 10, 'exam' => 30], // Adjusted to 55 (F)
    ['subject' => 'Economics', 'ca' => 20, 'test' => 15, 'exam' => 38], // Total: 73 (C)
    ['subject' => 'Art & Design', 'ca' => 29, 'test' => 19, 'exam' => 50], // Total: 98 (A)
];

// --- 2. Calculation Functions ---

/**
 * Maps a numerical score (0-100 percentage) to a standard letter grade.
 * @param int $percentage
 * @return string
 */
function getLetterGrade($percentage) {
    if ($percentage >= 90) return 'A';
    if ($percentage >= 80) return 'B';
    if ($percentage >= 70) return 'C';
    if ($percentage >= 60) return 'D';
    return 'F';
}

// The getAcademicRemark function was removed as the remark column is no longer used.


/**
 * Calculates the overall results (Average Percentage, Academic Status, and processes individual subject results).
 * @param array $results
 * @return array
 */
function calculateResults($results) {
    $total_subject_percentages = 0;
    $total_subjects = count($results);

    // 1. Process and calculate individual subject results
    $processed_results = [];
    foreach ($results as $item) {
        $total = $item['ca'] + $item['test'] + $item['exam'];
        // Since the components (CA+Test+Exam) are designed to total 100, the total is the percentage.
        $percentage = $total; 
        $grade = getLetterGrade($percentage);
        
        $item['total'] = $total;
        $item['percentage'] = $percentage;
        $item['grade'] = $grade;
        $processed_results[] = $item;

        $total_subject_percentages += $percentage;
    }
    
    // 2. Calculate overall average percentage
    $overall_average_percentage = $total_subjects > 0 ? $total_subject_percentages / $total_subjects : 0;
    
    // 3. Determine status (example: Promoted if average is 65% or higher)
    $status = $overall_average_percentage >= 65 ? 'PROMOTED' : 'REFERRED';
    $status_color = $overall_average_percentage >= 65 ? 'text-indigo-600' : 'text-red-600';

    return [
        'overall_percentage' => number_format($overall_average_percentage, 2),
        'total_subjects' => $total_subjects,
        'status' => $status,
        'status_color' => $status_color,
        'processed_results' => $processed_results
    ];
}

$final_results = calculateResults($results);
$display_results = $final_results['processed_results']; // Separate array for HTML iteration
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
        /* Custom font import and styling for report look */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #e5e7eb; /* Light gray background */
        }
        .report-container {
            /* Targeting A4 dimensions (8.27in x 11.69in) */
            max-width: 8.27in; 
            min-height: 11.69in;
            /* Uniform padding for screen display, removed Tailwind classes here */
            padding: 1.5rem; /* Reduced default padding for mobile screens */
        }
        @media (min-width: 768px) {
            .report-container {
                 padding: 2.5rem; /* Restore desktop padding */
            }
        }
        /* Print Optimization */
        @media print {
            body {
                background-color: #fff; /* White background for print */
                font-size: 9.5pt; /* Slightly smaller font for compression */
                margin: 0;
                padding: 0;
            }
            .print-hidden {
                display: none; /* Hide the print button when printing */
            }
            .report-container {
                box-shadow: none; /* Remove shadow when printing */
                border: none;
                /* Tight print margins for content to fit one page */
                padding: 0.4in; /* Adjusted for slightly more aggressive A4 fit */
                margin: 0;
                width: 100%;
                max-width: none;
                min-height: auto;
            }
            /* Adjusting font sizes to prevent overflow */
            h1 { font-size: 20pt !important; }
            h2 { font-size: 14pt !important; }
            .text-4xl { font-size: 20pt !important; }
            .text-xl { font-size: 14pt !important; }
            .text-lg { font-size: 12pt !important; }
            
            /* Tighter table and section spacing */
            .px-6, .py-3, .py-4, .px-3 { 
                padding: 0.2rem 0.4rem !important; /* Reduced padding further */
            }
            .mb-8, .mb-10 { margin-bottom: 0.5rem !important; }
            .mt-10 { margin-top: 0.5rem !important; }
            /* Ensure all table cells are visible for print */
            .sm\:table-cell { display: table-cell !important; }
            .sm\:hidden { display: none !important; }
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="p-4 md:p-8 flex justify-center">

    <div class="report-container w-full bg-white shadow-2xl rounded-xl overflow-hidden">
        
        <header class="border-b-4 border-blue-700 pb-4 mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-2xl md:text-4xl font-extrabold text-blue-800 tracking-tight">
                    Secondary School Academic Report
                </h1>
                <p class="text-sm md:text-lg text-gray-600 mt-1">
                    [High School / Secondary School Name Placeholder]
                </p>
            </div>

            <button 
                onclick="window.print()" 
                class="print-hidden flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-3 text-sm rounded-lg shadow-md transition duration-150 ease-in-out transform hover:scale-105 mt-2 md:mt-0"
                title="Download PDF"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" x2="12" y1="15" y2="3"/>
                </svg>
                Download
            </button>
        </header>

        <section class="mb-10 p-4 sm:p-6 bg-gray-50 rounded-xl border border-gray-200">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4 border-b pb-2">Student Profile</h2>
            
            <div class="flex flex-col md:flex-row items-start gap-6 md:gap-8"> 
                <div class="flex-shrink-0 mx-auto md:mx-0">
                    <img 
                        src="https://placehold.co/140x170/e5e7eb/4b5563?text=STUDENT" 
                        alt="Student Photo" 
                        class="w-28 h-36 sm:w-36 sm:h-44 object-cover rounded-lg border-4 border-white shadow-xl"
                        onerror="this.onerror=null;this.src='https://placehold.co/140x170/e5e7eb/4b5563?text=STUDENT';"
                    >
                </div>

                <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-4 gap-x-6 text-sm w-full">
                    <div class="border-b pb-2">
                        <p class="font-semibold text-gray-600 uppercase text-xs">Full Name</p>
                        <p class="font-extrabold text-lg text-blue-800 mt-1"><?php echo htmlspecialchars($student_data['name']); ?></p>
                    </div>
                    <div class="border-b pb-2">
                        <p class="font-semibold text-gray-600 uppercase text-xs">Student ID</p>
                        <p class="font-bold text-md text-gray-900 mt-1"><?php echo htmlspecialchars($student_data['student_id']); ?></p>
                    </div>
                    <div class="border-b pb-2">
                        <p class="font-semibold text-gray-600 uppercase text-xs">Grade Level</p>
                        <p class="font-bold text-md text-gray-900 mt-1"><?php echo htmlspecialchars($student_data['grade_level']); ?></p>
                    </div>
                    <div class="border-b pb-2">
                        <p class="font-semibold text-gray-600 uppercase text-xs">Term</p>
                        <p class="text-gray-900 mt-1"><?php echo htmlspecialchars($student_data['term']); ?></p>
                    </div>
                    <div class="border-b pb-2">
                        <p class="font-semibold text-gray-600 uppercase text-xs">Report Date</p>
                        <p class="text-gray-900 mt-1"><?php echo htmlspecialchars($student_data['date_generated']); ?></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-8">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4">Course Performance</h2>
            <div class="overflow-x-auto shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">
                                Subject
                            </th>
                            <th scope="col" class="hidden sm:table-cell px-3 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">
                                CA
                            </th>
                            <th scope="col" class="hidden sm:table-cell px-3 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">
                                Test
                            </th>
                            <th scope="col" class="hidden sm:table-cell px-3 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">
                                Exam
                            </th>
                            <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">
                                Total
                            </th>
                            <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">
                                %
                            </th>
                            <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-bold text-blue-700 uppercase tracking-wider">
                                Grade
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($display_results as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($item['subject']); ?>
                                    
                                    <div class="sm:hidden text-xs text-gray-500 font-normal mt-1">
                                        CA: <?php echo htmlspecialchars($item['ca']); ?>, Test: <?php echo htmlspecialchars($item['test']); ?>, Exam: <?php echo htmlspecialchars($item['exam']); ?>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell px-3 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                    <?php echo htmlspecialchars($item['ca']); ?>
                                </td>
                                <td class="hidden sm:table-cell px-3 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                    <?php echo htmlspecialchars($item['test']); ?>
                                </td>
                                <td class="hidden sm:table-cell px-3 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                    <?php echo htmlspecialchars($item['exam']); ?>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-blue-800">
                                    <?php echo htmlspecialchars($item['total']); ?>
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-center font-semibold 
                                    <?php echo $item['percentage'] >= 60 ? 'text-green-600' : 'text-red-500'; ?>">
                                    <?php echo number_format($item['percentage'], 2); ?>%
                                </td>
                                <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-gray-900">
                                    <?php echo htmlspecialchars($item['grade']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="mb-8 pt-4 border-t-2 border-gray-200">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 mb-4">Summary & Key Metrics</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
                <div class="bg-green-50 p-4 sm:p-6 rounded-xl border border-green-200 shadow-lg text-center">
                    <p class="text-xs sm:text-sm font-semibold text-green-700 uppercase">Overall Average Percentage</p>
                    <p class="text-3xl sm:text-4xl font-extrabold text-green-600 mt-1">
                        <?php echo $final_results['overall_percentage']; ?>%
                    </p>
                </div>
                
                <div class="p-4 sm:p-6 rounded-xl border shadow-lg text-center 
                    <?php echo $final_results['status'] == 'PROMOTED' ? 'bg-indigo-50 border-indigo-200' : 'bg-red-50 border-red-200'; ?>">
                    <p class="text-xs sm:text-sm font-semibold 
                        <?php echo $final_results['status'] == 'PROMOTED' ? 'text-indigo-700' : 'text-red-700'; ?> 
                        uppercase">
                        Academic Status
                    </p>
                    <p class="text-3xl sm:text-4xl font-extrabold mt-1 
                        <?php echo $final_results['status_color']; ?>">
                        <?php echo htmlspecialchars($final_results['status']); ?>
                    </p>
                </div>

                <div class="bg-yellow-50 p-4 sm:p-6 rounded-xl border border-yellow-200 shadow-lg text-center">
                    <p class="text-xs sm:text-sm font-semibold text-yellow-700 uppercase">Total Subjects Taken</p>
                    <p class="text-3xl sm:text-4xl font-extrabold text-yellow-600 mt-1">
                        <?php echo $final_results['total_subjects']; ?>
                    </p>
                </div>
            </div>

            <div class="mt-6 sm:mt-8 text-xs sm:text-sm text-gray-600 border-t pt-4">
                <p class="font-semibold mb-2">Grading Scale Key:</p>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-1 text-center">
                    <div class="p-2 bg-gray-100 rounded-md">A: 90-100</div>
                    <div class="p-2 bg-gray-100 rounded-md">B: 80-89</div>
                    <div class="p-2 bg-gray-100 rounded-md">C: 70-79</div>
                    <div class="p-2 bg-gray-100 rounded-md">D: 60-69</div>
                    <div class="p-2 bg-gray-100 rounded-md">F: Below 60</div>
                </div>
                <p class="mt-4 text-xs italic">Note: Academic Status is determined by an overall average of 65% or higher.</p>
            </div>
        </section>

        <footer class="mt-8 md:mt-10 pt-4 border-t border-gray-300 text-xs text-gray-500 text-center">
            <p>Report Generated on: <?php echo htmlspecialchars($student_data['date_generated']); ?></p>
            <p class="mt-1">This document is the official academic record for the specified term.</p>
        </footer>

    </div>

</body>
</html>