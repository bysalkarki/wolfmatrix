<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCsvImportRequest;
use App\Services\CsvImport;

class ImportUserCsvController extends Controller
{
    public function __construct(public CsvImport $csvImporter) {}

    public function import(UserCsvImportRequest $request)
    {
        $filePath = $request->file("file")->getRealPath();
        $result = $this->csvImporter->import($filePath);

        return response()->json($result, isset($result["errors"]) ? 422 : 200);
    }
}
