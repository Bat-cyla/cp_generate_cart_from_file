<?php
/*****************************************************************************
 *                                                        © 2013 Cart-Power   *
 *           __   ______           __        ____                             *
 *          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
 *      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
 *     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
 *    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
 *                                                                            *
 *                                                                            *
 * -------------------------------------------------------------------------- *
 * This is commercial software, only users who have purchased a valid license *
 * and  accept to the terms of the License Agreement can install and use this *
 * program.                                                                   *
 * -------------------------------------------------------------------------- *
 * website: https://store.cart-power.com                                      *
 * email:   sales@cart-power.com                                              *
 ******************************************************************************/

namespace Tygh\CpFileReader;
require_once 'app/addons/price_list/lib/phpexcel/Classes/PHPExcel.php';

use PHPExcel_Reader_CSV;
use stdClass;
use PHPExcel_IOFactory;

class CpFileReader
{
    private object $readerExcel;
    private object $readerCSV;
    private string $file;
    private string $ext;

    public function __construct(string $file_path, string $ext)
    {
        $this->file = DIR_ROOT . '/' . $file_path;
        $this->ext = $ext;
    }

    /**
     * @param array $array_fields
     *
     * @return array|bool
     * @throws \PHPExcel_Reader_Exception
     */
    public function read(array $array_fields = [])
    {
        if ($this->ext == 'xls' || $this->ext == 'xlsx') {
            $this->readerExcel = PHPExcel_IOFactory::load($this->file);
            $res = $this->getTableData($array_fields, $this->readerExcel);

            return $res;
        }

        if ($this->ext == 'csv') {
            $objReader = new PHPExcel_Reader_CSV();
            $objReader->setDelimiter(';');
            $objReader->setEnclosure('');
            $objReader->setSheetIndex(0);

            $this->readerCSV = $objReader->load($this->file);
            $res = $this->getTableData($array_fields, $this->readerCSV);

            return $res;
        }
    }

    /**
     * @param array  $array_fields
     * @param object $reader
     *
     * @return array
     * @throws CpXlsColumnException
     */
    protected function getTableData(array $array_fields, object $reader)
    {
        foreach ($array_fields as $key => $value) {
            if (preg_match('/^[a-zA-Z]{1,3}$|^\d{1,5}$/', $value)) {
                $converted = $this->sanitizeColumnName($value);
                if ($converted === false) {
                    throw new CpXlsColumnException(__('wrong_column_name', ['[column_name]' => $value]));
                }
                $array_fields[$key] = $converted;
            } elseif ($key!='name' && empty($value)) {
                throw new CpXlsColumnException(__('wrong_column_name', ['[column_name]' => $value]));
            }
        }

        $res = [];

        $start = 1;

        $sheet = $reader->getActiveSheet();
        $upto = $sheet->getHighestRow($array_fields['artiqle']);
        //get data
        for ($i = $start; $i <= $upto; $i++) {
            $row = new stdClass();

            if (!empty($array_fields['name'])) {
                $row->name = $sheet->getCell($array_fields['name'] . $i)->getValue();
            }
            $row->artiqle = $sheet->getCell($array_fields['artiqle'] . $i)->getValue();
            $row->amount = $sheet->getCell($array_fields['amount'] . $i)->getValue();

            //continue empty artiqle
            if ($row->artiqle == null) continue;
            //continue if amount non numeric
            if (!is_numeric($row->amount)) continue;

            $res[] = json_decode(json_encode($row), true);
        }
        return $res;
    }

    /**
     * @param string|int
     *
     * @return string|bool
     */
    protected function sanitizeColumnName($value)
    {   //18278 => ZZZ
        if (!preg_match('/^\d{1,5}$/', $value)) {
            return $value;
        }

        if ($value < 1 || $value > 18278) {
            return false;
        }

        return $this->conv($value);
    }

    /**
     * Convert integer column number to letter code
     * https://learn.microsoft.com/en-us/office/troubleshoot/excel/convert-excel-column-numbers
     *
     * @param int $col
     *
     * @return string
     */
    private function conv(int $col):string {
        $res = '';
        while ($col>0) {
            $a = intval(($col - 1) / 26);
            $b = ($col - 1) % 26;
            $res = chr($b + 65) . $res;
            $col = $a;
        }

        return $res;
    }

}