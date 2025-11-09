<?php
require_once '../includes/validate.php';
require '../includes/name.php';
require_once 'log_activity.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Admin', 'Event Manager'])) {
    header("location:home.php");
    exit();
}

require '../includes/connect.php';

// Fetch data from database
$query = $conn->query("
    SELECT 
        r.id,
        r.full_name, 
        r.reservation_type, 
        r.checkin_date,
        r.checkout_date,
        b.total_amount, 
        b.balance, 
        b.payment_method,
        b.status AS billing_status 
    FROM `reservations` r 
    JOIN `billing` b ON r.id = b.reservation_id 
    WHERE r.status = 'Checked-out' 
    ORDER BY r.checkin_date DESC
");

$rows = array();
$total_sales = 0;

// Add headers
$rows[] = array('Reservation ID','Customer Name', 'Reservation Type', 'Check-in Date', 'Checkout Date', 'Total Amount', 'Balance', 'Payment Method', 'Status');

// Add data rows
while ($fetch = $query->fetch_assoc()) {
    $total_sales += $fetch['total_amount'];
    $rows[] = array(
        $fetch['id'],
        $fetch['full_name'],
        $fetch['reservation_type'],
        date("M d, Y", strtotime($fetch['checkin_date'])),
        date("M d, Y", strtotime($fetch['checkout_date'])),
        $fetch['total_amount'],
        $fetch['balance'],
        !empty($fetch['payment_method']) ? $fetch['payment_method'] : "N/A",
        $fetch['billing_status']
    );
}

// Add total row
$rows[] = array('', '', '', 'Total Sales: ₱', $total_sales, '', '', '');

// Create Excel XML content (SpreadsheetML format)
$excelContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$excelContent .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
$excelContent .= '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office"><Author>Renato\'s Place</Author><Created>' . date('Y-m-d\TH:i:s\Z') . '</Created></DocumentProperties>' . "\n";
$excelContent .= '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel"><WindowHeight>9000</WindowHeight><WindowWidth>13860</WindowWidth><ProtectStructure>False</ProtectStructure><ProtectWindows>False</ProtectWindows></ExcelWorkbook>' . "\n";
$excelContent .= '<Styles>' . "\n";
$excelContent .= '<Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Bottom"/><Borders/><Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/><Interior/><NumberFormat/><Protection/></Style>' . "\n";
$excelContent .= '<Style ss:ID="Header"><Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/><Interior ss:Color="#366092" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/></Style>' . "\n";
$excelContent .= '<Style ss:ID="Currency"><NumberFormat ss:Format="[DBNum1][$₱-409]#,##0.00_);([DBNum1][$₱-409]#,##0.00)"/><Alignment ss:Horizontal="Right"/></Style>' . "\n";
$excelContent .= '<Style ss:ID="Total"><Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Bold="1"/><Interior ss:Color="#E8E8E8" ss:Pattern="Solid"/><NumberFormat ss:Format="[DBNum1][$₱-409]#,##0.00_);([DBNum1][$₱-409]#,##0.00)"/><Alignment ss:Horizontal="Right"/></Style>' . "\n";
$excelContent .= '</Styles>' . "\n";
$excelContent .= '<Worksheet ss:Name="Sales Record">' . "\n";
$excelContent .= '<Table ss:ExpandedColumnCount="9" ss:ExpandedRowCount="' . (count($rows)) . '" x:FullColumns="1" x:FullRows="1">' . "\n";

// Add column definitions with widths
$excelContent .= '<Column ss:Index="1" ss:Width="80"/>' . "\n";
$excelContent .= '<Column ss:Index="2" ss:Width="150"/>' . "\n";
$excelContent .= '<Column ss:Index="3" ss:Width="130"/>' . "\n";
$excelContent .= '<Column ss:Index="4" ss:Width="110"/>' . "\n";
$excelContent .= '<Column ss:Index="5" ss:Width="110"/>' . "\n";
$excelContent .= '<Column ss:Index="6" ss:Width="110"/>' . "\n";
$excelContent .= '<Column ss:Index="7" ss:Width="110"/>' . "\n";
$excelContent .= '<Column ss:Index="8" ss:Width="130"/>' . "\n";
$excelContent .= '<Column ss:Index="9" ss:Width="110"/>' . "\n";

// Add rows
foreach ($rows as $rowIndex => $row) {
    $isHeader = ($rowIndex === 0);
    $isTotal = ($rowIndex === count($rows) - 1);
    $rowHeight = $isHeader ? '25' : '18';
    
    $excelContent .= '<Row ss:Height="' . $rowHeight . '">' . "\n";
    
    foreach ($row as $colIndex => $cell) {
        $isCurrencyCol = ($colIndex === 5 || $colIndex === 6);
        $styleID = 'Default';
        
        if ($isHeader) {
            $styleID = 'Header';
        } elseif ($isTotal && $isCurrencyCol) {
            $styleID = 'Total';
        } elseif ($isCurrencyCol && !$isHeader && !$isTotal) {
            $styleID = 'Currency';
        }
        
        $cellValue = htmlspecialchars($cell);
        $excelContent .= '<Cell ss:StyleID="' . $styleID . '"><Data ss:Type="String">' . $cellValue . '</Data></Cell>' . "\n";
    }
    
    $excelContent .= '</Row>' . "\n";
}

$excelContent .= '</Table>' . "\n";
$excelContent .= '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel"><Print><ValidPrinterInfo/><HorizontalResolution>200</HorizontalResolution><VerticalResolution>200</VerticalResolution></Print><Selected/><ProtectObjects>False</ProtectObjects><ProtectScenarios>False</ProtectScenarios></WorksheetOptions>' . "\n";
$excelContent .= '</Worksheet>' . "\n";
$excelContent .= '</Workbook>';

// Set headers for download
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="sales_record_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Length: ' . strlen($excelContent));

// Output content
echo $excelContent;

log_activity($_SESSION['admin_id'], 'Sales Management', 'Exported sales record to Excel.');
exit();
?>