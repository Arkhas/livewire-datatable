<?php

namespace Arkhas\LivewireDatatable\Export;

use Illuminate\Support\Collection;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

class DatatableExporter
{
    protected EloquentTable $table;
    protected array $filters;
    protected ?string $search;

    public function __construct(EloquentTable $table, array $filters = [], ?string $search = null)
    {
        $this->table = $table;
        $this->filters = $filters;
        $this->search = $search;
    }

    /**
     * Export to CSV.
     */
    public function toCsv(): StreamedResponse
    {
        $filename = $this->table->getExportName() . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // Headers
            $headers = $this->getExportHeaders();
            fputcsv($handle, $headers);

            // Data
            $this->table->buildQuery($this->filters, $this->search)
                ->chunk(config('livewire-datatable.export.chunk_size', 1000), function ($rows) use ($handle) {
                    foreach ($rows as $row) {
                        fputcsv($handle, $this->getExportRow($row));
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export to XLSX (requires maatwebsite/excel package).
     */
    public function toXlsx(): Response
    {
        // @codeCoverageIgnoreStart
        // Check if Laravel Excel is installed
        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            throw new \RuntimeException('XLSX export requires the maatwebsite/excel package. Install it with: composer require maatwebsite/excel');
        }
        // @codeCoverageIgnoreEnd

        $filename = $this->table->getExportName() . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new DatatableExport($this->table, $this->filters, $this->search),
            $filename
        );
    }

    /**
     * Get the export headers.
     */
    protected function getExportHeaders(): array
    {
        $headers = [];

        foreach ($this->table->getColumns() as $column) {
            // Skip checkbox and action columns
            $type = $column->toArray()['type'];
            if ($type === 'checkbox' || $type === 'action') {
                continue;
            }

            $headers[] = $column->getLabel();
        }

        return $headers;
    }

    /**
     * Get the export row data.
     */
    protected function getExportRow($row): array
    {
        $data = [];

        foreach ($this->table->getColumns() as $column) {
            // Skip checkbox and action columns
            $type = $column->toArray()['type'];
            if ($type === 'checkbox' || $type === 'action') {
                continue;
            }

            $data[] = $column->getExportValue($row);
        }

        return $data;
    }

    /**
     * Export based on format.
     */
    public function export(string $format = 'csv'): Response
    {
        return match ($format) {
            'xlsx' => $this->toXlsx(),
            default => $this->toCsv(),
        };
    }
}
