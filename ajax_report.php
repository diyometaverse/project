<?php
include "db.php";
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Generate Report Data based on type
 */
function generateReportData($conn, $type) {
    switch ($type) {
        case 'payroll':
            $query = "
                SELECT 
                    e.employee_id,
                    CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                    SUM(p.total_hours) AS totalHours,
                    SUM(p.base_salary) AS totalBaseSalary,
                    SUM(p.overtime) AS totalOvertime,
                    SUM(p.deductions) AS totalDeductions,
                    SUM(p.sss) AS totalSSS,
                    SUM(p.philhealth) AS totalPhilhealth,
                    SUM(p.pagibig) AS totalPagibig,
                    SUM(p.withholding_tax) AS totalTax,
                    SUM(p.gross_pay) AS totalGross,
                    SUM(p.net_pay) AS totalNet
                FROM payroll p
                JOIN employees e ON e.employee_id = p.employee_id
                GROUP BY e.employee_id
            ";
            break;

        case 'attendance':
            $query = "
                SELECT 
                    e.employee_id,
                    CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                    COUNT(a.attendance_id) AS totalClockIns,
                    AVG(TIME_TO_SEC(TIMEDIFF(a.time_out, a.time_in)) / 3600) AS avgHours
                FROM attendance a
                JOIN employees e ON e.employee_id = a.employee_id
                GROUP BY e.employee_id
            ";
            break;

        case 'compliance':
            $query = "
                SELECT 
                    e.employee_id,
                    CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
                    COUNT(a.attendance_id) AS totalRecords,
                    SUM(CASE WHEN a.time_out IS NULL THEN 1 ELSE 0 END) AS missingTimeOuts
                FROM attendance a
                JOIN employees e ON e.employee_id = a.employee_id
                GROUP BY e.employee_id
            ";
            break;

        default:
            return [];
    }

    $result = $conn->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// --- MAIN HANDLER ---
if (isset($_GET['report'])) {
    $reportType = $_GET['report'];
    $reportData = generateReportData($conn, $reportType);

    /**
     * EXPORT PDF
     */
    if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
        $dompdf = new Dompdf();
        $html = "<h2 style='text-align:center;'>" . ucfirst($reportType) . " Report</h2>";

        if ($reportType === "payroll") {
            $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                        <thead>
                            <tr style='background:#f2f2f2;'>
                                <th>Employee</th><th>Total Hours</th><th>Base Salary</th><th>Overtime</th>
                                <th>Deductions</th><th>SSS</th><th>PhilHealth</th><th>Pag-IBIG</th>
                                <th>Tax</th><th>Gross</th><th>Net</th>
                            </tr>
                        </thead><tbody>";
            foreach ($reportData as $row) {
                $html .= "<tr>
                            <td>{$row['employee_name']}</td>
                            <td>{$row['totalHours']}</td>
                            <td>P".number_format($row['totalBaseSalary'],2)."</td>
                            <td>P".number_format($row['totalOvertime'],2)."</td>
                            <td>P".number_format($row['totalDeductions'],2)."</td>
                            <td>P".number_format($row['totalSSS'],2)."</td>
                            <td>P".number_format($row['totalPhilhealth'],2)."</td>
                            <td>P".number_format($row['totalPagibig'],2)."</td>
                            <td>P".number_format($row['totalTax'],2)."</td>
                            <td>P".number_format($row['totalGross'],2)."</td>
                            <td><strong>P".number_format($row['totalNet'],2)."</strong></td>
                          </tr>";
            }
            $html .= "</tbody></table>";
        } elseif ($reportType === "attendance") {
            $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                        <thead>
                            <tr style='background:#f2f2f2;'>
                                <th>Employee</th><th>Total Clock-ins</th><th>Average Hours</th>
                            </tr>
                        </thead><tbody>";
            foreach ($reportData as $row) {
                $html .= "<tr>
                            <td>{$row['employee_name']}</td>
                            <td>{$row['totalClockIns']}</td>
                            <td>".number_format($row['avgHours'],2)."</td>
                        </tr>";
            }
            $html .= "</tbody></table>";
        } elseif ($reportType === "compliance") {
            $html .= "<table border='1' cellspacing='0' cellpadding='5' width='100%'>
                        <thead>
                            <tr style='background:#f2f2f2;'>
                                <th>Employee</th><th>Total Records</th><th>Missing Time-outs</th>
                            </tr>
                        </thead><tbody>";
            foreach ($reportData as $row) {
                $html .= "<tr>
                            <td>{$row['employee_name']}</td>
                            <td>{$row['totalRecords']}</td>
                            <td>{$row['missingTimeOuts']}</td>
                        </tr>";
            }
            $html .= "</tbody></table>";
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream(ucfirst($reportType) . "_report.pdf", ["Attachment" => true]);
        exit;
    }

    /**
     * EXPORT EXCEL
     */
    if (isset($_GET['export']) && $_GET['export'] === 'excel') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title
        $sheet->setCellValue('A1', ucfirst($reportType) . " Report");

        if ($reportType === "payroll") {
            $headers = ['Employee', 'Total Hours', 'Base Salary', 'Overtime', 'Deductions', 'SSS', 'PhilHealth', 'Pag-IBIG', 'Tax', 'Gross', 'Net'];
        } elseif ($reportType === "attendance") {
            $headers = ['Employee', 'Total Clock-ins', 'Average Hours'];
        } else {
            $headers = ['Employee', 'Total Records', 'Missing Time-outs'];
        }

        // Headers
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'3', $header);
            $col++;
        }

        // Data
        $row = 4;
        foreach ($reportData as $emp) {
            $col = 'A';
            foreach ($emp as $value) {
                $sheet->setCellValue($col.$row, $value);
                $col++;
            }
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . ucfirst($reportType) . '_report.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * DEFAULT JSON RESPONSE
     */
    header('Content-Type: application/json');
    echo json_encode($reportData);
    exit;
}
?>
