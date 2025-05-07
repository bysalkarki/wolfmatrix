<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use SplFileObject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CsvImport
{
    protected const DEFAULT_PASSWORD = "password";

    protected string $password;
    protected int $batchSize = 500;

    public function import(string $filePath): array
    {
        $this->password = Hash::make(self::DEFAULT_PASSWORD);
        $errors = [];
        $rowNum = 0;
        $validRows = [];

        $csv = new SplFileObject($filePath);
        $csv->setFlags(SplFileObject::READ_CSV);
        $csv->setCsvControl(",");

        $expectedHeaders = ["name", "phone", "email"];

        foreach ($csv as $row) {
            $rowNum++;

            if ($row === [null] || $row === false) {
                continue;
            }

            if ($rowNum === 1) {
                $headers = array_map("trim", $row);
                if ($headers !== $expectedHeaders) {
                    return [
                        "error" =>
                            "Invalid CSV headers. Expected: " .
                            implode(", ", $expectedHeaders),
                    ];
                }
                continue;
            }

            $rowData = array_combine($expectedHeaders, array_map("trim", $row));

            $validationResult = Validator::make($rowData, [
                "name" => "required|string|max:255",
                "email" => "required|email|unique:users,email",
                "phone" => [
                    "required",
                    "unique:users,phone",
                    new \App\Rules\InternationalPhone(),
                ],
            ]);

            if ($validationResult->fails()) {
                $errors[$rowNum] = $validationResult->errors()->all();
                continue;
            }

            $validRows[] = [
                "name" => $rowData["name"],
                "email" => $rowData["email"],
                "phone" => $rowData["phone"],
                "password" => $this->password,
                "created_at" => now(),
                "updated_at" => now(),
            ];

            if (count($validRows) >= $this->batchSize) {
                $this->importRowsInBulk($validRows);
                $validRows = [];
            }
        }

        if (!empty($validRows)) {
            $this->importRowsInBulk($validRows);
        }

        return empty($errors)
            ? ["message" => "CSV imported successfully."]
            : ["errors" => $errors];
    }

    /**
     * Perform a bulk insert into the database.
     *
     * @param array $validRows
     * @return void
     */
    private function importRowsInBulk(array $validRows): void
    {
        try {
            DB::table("users")->insert($validRows);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            throw new \Exception("Failed to insert data: " . $e->getMessage());
        }
    }
}
