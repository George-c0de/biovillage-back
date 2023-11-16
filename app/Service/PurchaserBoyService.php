<?php

namespace App\Service;

use App\Models\TagModel;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/**
 *
 * Total purchasing
 *
 * @package App\Service\GroupService
 */
class PurchaserBoyService extends BaseService
{

    /**
     * Return tags list for show clients
     * @param $params
     * @param $products
     * @param $gifts
     * @return Xlsx
     */
    public static function exportToExcel($params, $products, $gifts) {

        $date = $params['date'];
        $startHour = $params['startHour'] ?? 0;
        $endHour = $params['endHour'] ?? 24;
        $orderStatus = $params['orderStatus'] ?? '';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Main header
        $sheet->mergeCells("A1:F1");
        $sheet->setCellValue('A1', trans('purchases.title', ['date' => $date]) );
        $sheet->getStyle("A1")->getFont()->setSize(18);

        $sheet->mergeCells("A2:F2");
        $sheet->setCellValue('A2', trans('purchases.hoursInterval', [
            'hourBegin' => $startHour,
            'hourEnd' => $endHour
        ]));
        $sheet->getStyle("A2")->getFont()->setSize(18);

        // Products
        $sheet->mergeCells("A4:F4");
        $sheet->setCellValue('A4', trans('purchases.productsTitle'));
        $sheet->getStyle("A4")->getFont()->setSize(18);

        // Product columns
        $sheet->setCellValue('A5', trans('purchases.colProdName'));
        $sheet->setCellValue('B5', trans('purchases.colProdPurchasesCount'));
        $sheet->setCellValue('C5', trans('purchases.colProdPrice'));
        $sheet->setCellValue('D5', trans('purchases.colProdUnit'));
        $sheet->setCellValue('E5', trans('purchases.colProdTotalQty'));
        $sheet->setCellValue('F5', trans('purchases.colProdTotal'));

        // Products
        $row = 6;
        foreach ($products as $prod) {
            $sheet->setCellValue('A' . $row , $prod['productName']);
            $sheet->setCellValue('B' . $row , $prod['totalQty']);
            $sheet->setCellValue('C' . $row , $prod['price']);
            $sheet->setCellValue('D' . $row , $prod['unitFullName']);
            $sheet->setCellValue('E' . $row , $prod['totalUnits']);
            $sheet->setCellValue('F' . $row , $prod['total']);

            $row++;
        }


        // Gifts header
        $sheet->mergeCells( "A$row:F$row");
        $sheet->setCellValue("A$row", trans('purchases.productsTitle'));
        $sheet->getStyle("A$row")->getFont()->setSize(18);

        // Product columns
        $row++;
        $sheet->setCellValue("A$row", trans('purchases.colGiftName'));
        $sheet->setCellValue("B$row", trans('purchases.colGiftTotalQty'));
        $sheet->setCellValue("C$row", trans('purchases.colGiftBonuses'));
        $sheet->setCellValue("D$row", trans('purchases.colGiftTotalBonuses'));


        // Gifts
        $row++;
        foreach ($gifts as $gift) {
            $sheet->setCellValue('A' . $row , $gift['giftName']);
            $sheet->setCellValue('B' . $row , $gift['totalQty']);
            $sheet->setCellValue('C' . $row , $gift['bonuses']);
            $sheet->setCellValue('D' . $row , $gift['totalBonuses']);

            $row++;
        }

        return new Xlsx($spreadsheet);
    }
}
