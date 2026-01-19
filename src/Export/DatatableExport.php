<?php

namespace Arkhas\LivewireDatatable\Export;

use Illuminate\Support\Collection;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class DatatableExport implements FromGenerator, WithHeadings, WithChunkReading
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
     * @return \Generator
     */
    public function generator(): \Generator
    {
        $query = $this->table->buildQuery($this->filters, $this->search);

        foreach ($query->lazy(config('livewire-datatable.export.chunk_size', 1000)) as $row) {
            yield $this->getExportRow($row);
        }
    }

    /**
     * @return array
     */
    public function headings(): array
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
     * @return int
     */
    public function chunkSize(): int
    {
        return config('livewire-datatable.export.chunk_size', 1000);
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
}
