<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($fileTmpPath);
        $headers = ['QTY', 'Unit Code', 'Description', 'Unit Price', 'Total'];
        $allData = [];

        foreach ($spreadsheet->getSheetNames() as $sheetIndex => $sheetName) {
            $worksheet = $spreadsheet->getSheet($sheetIndex);
            $startProcessing = false;
            $headerIndex = [];
            $currentProduct = null;
            $currentDescription = [];
            $lastUnitPrice = 0;
            $lastQty = 0;

            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $rowData = [];

                foreach ($cellIterator as $cell) {
                    $rowData[] = trim($cell->getValue());
                }

                if (!$startProcessing) {
                    $foundHeaders = array_intersect($headers, $rowData);
                    if (count($foundHeaders) === count($headers)) {
                        foreach ($headers as $header) {
                            $headerIndex[$header] = array_search($header, $rowData);
                        }
                        $startProcessing = true;
                        continue;
                    }
                }

                if ($startProcessing && !empty($headerIndex)) {
                    $qty = $rowData[$headerIndex['QTY']] ?? "";
                    $unitCode = $rowData[$headerIndex['Unit Code']] ?? "";
                    $description = $rowData[$headerIndex['Description']] ?? "";
                    $unitPrice = $rowData[$headerIndex['Unit Price']] ?? "";
                    $total = $rowData[$headerIndex['Total']] ?? "";

                    if (!is_numeric($qty) && !empty($qty)) {
                        continue;
                    }

                    $unitPrice = is_numeric($unitPrice) ? $unitPrice : 0;

                    if (!empty($total)) {
                        if ($currentProduct) {
                            $allData[] = [
                                'QTY' => $lastQty,
                                'UnitCode' => $currentProduct,
                                'Description' => implode("\n", $currentDescription),
                                'Price' => $lastUnitPrice,
                                'Total' => $total
                            ];
                        }

                        // Set new product data
                        $currentProduct = $unitCode;
                        $currentDescription = [$description];
                        $lastUnitPrice = $unitPrice;

                        if (is_numeric($qty) && $qty !== "") {
                            $lastQty = (int)$qty;
                        }
                    } else {
                        if (!empty($description)) {
                            $currentDescription[] = $description;
                        }
                    }
                }
            }

            if ($currentProduct) {
                $allData[] = [
                    'QTY' => $lastQty,
                    'UnitCode' => $currentProduct,
                    'Description' => implode("\n", $currentDescription),
                    'Price' => $lastUnitPrice,
                    'Total' => $total
                ];
            }
        }

        echo json_encode(["status" => "success", "data" => $allData], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "File upload failed"]);
}
?>
