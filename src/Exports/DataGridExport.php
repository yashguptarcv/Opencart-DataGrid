<?php

namespace RCV\DataGrid\Exports;

use RCV\DataGrid\DataGrid;

class DataGridExport
{
    /**
     * DataGrid instance
     *
     * @var \RCV\DataGrid\DataGrid
     */
    protected $datagrid;

    /**
     * Constructor for DataGridExport class.
     *
     * @param \RCV\DataGrid\DataGrid $datagrid
     * @return void
     */
    public function __construct(DataGrid $datagrid)
    {
        $this->datagrid = $datagrid;
    }

    public function exportData($records, $filename, $extension)
    {
        if ($extension == 'csv') {
            $this->exportToCSV($records, $filename);
        } elseif ($extension == 'xml') {
            $this->exportToXml($records, $filename);
        }
    }

    /**
     * Export the data to a CSV file.
     *
     * @param array $params
     * @return void
     */
    public function exportToCSV($records, $filename)
    {
        // Send headers to download the file as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename . '');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open the output stream for CSV
        $output = fopen('php://output', 'w');

        // Get column headings
        $headings = [];
        foreach ($this->datagrid->getColumns() as $column) {
            if ($column->getExportable()) {
                // get image
                $headings[] = $column->getLabel();

                if ($image = $column->getImage()) {
                    $headings[] = __('wk_datagrid.image');
                }

                // get attachment
                if ($attachment = $column->getAttachment()) {
                    $headings[] = __('wk_datagrid.attachments');
                }
            }
        }

        // Write the headers to the CSV
        fputcsv($output, $headings);

        // Write the records to the CSV
        foreach ($records['records'] as $record) {
            $row = [];
            foreach ($this->datagrid->getColumns() as $column) {
                if ($column->getExportable()) {

                    if ($closure = $column->getClosure()) {
                        $index_name = explode('.', $column->getIndex())[1] ?? $column->getIndex();
                        $record->{$index_name} =  strip_tags($closure($record));
                    }


                    $index_name = explode('.', $column->getIndex())[1] ?? $column->getIndex();
                    $row[] = $record->{$index_name};
                    // get image
                    if ($image = $column->getImage()) {
                        $imageDetail = $image($record);
                        if (!empty($imageDetail['object_id']) && !empty($imageDetail['object_type'])) {
                            $imageData = ''; //fn_get_image_pairs($imageDetail['object_id'], $imageDetail['object_type'], 'M', true, true, CART_LANGUAGE);
                            $row[] = $record->{'Image'} = $imageData['detailed']['image_path'] ?? '';
                        } else {
                            throw new \Exception('Image should return 2 values: object_id and object_type');
                        }
                    }

                    // get attachment
                    if ($attachment = $column->getAttachment()) {

                        $attachmentDetail = $attachment($record);
                        if (!empty($attachmentDetail['object_id']) && !empty($attachmentDetail['object_type'])) {
                            $attachmentData = ['filename' => 'test.png']; //fn_get_attachments($attachmentDetail['object_type'], $attachmentDetail['object_id'], 'M', CART_LANGUAGE);
                            $row[] = $record->{'Attachments'} = implode('// ', array_column($attachmentData, 'filename')) ?? [];
                        } else {
                            throw new \Exception('Attachment should return 2 values: object_id and object_type');
                        }
                    }
                }
            }
            fputcsv($output, $row);
        }
        // Close the output stream
        fclose($output);
        exit;
    }

    public function exportToXml($records, $filename)
    {

        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename . '');
        header('Pragma: no-cache');
        header('Expires: 0');

        $xml = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');

        foreach ($records['records'] as $record) {
            $item = $xml->addChild('record');
            foreach ($this->datagrid->getColumns() as $column) {
                if ($column->getExportable()) {
                    if ($closure = $column->getClosure()) {
                        $index_name = explode('.', $column->getIndex())[1] ?? $column->getIndex();
                        $record->{$index_name} =  strip_tags($closure($record));
                    }

                    // get image
                    if ($image = $column->getImage()) {
                        $imageDetail = $image($record);
                        if (!empty($imageDetail['object_id']) && !empty($imageDetail['object_type'])) {
                            $imageData = ''; //fn_get_image_pairs($imageDetail['object_id'], $imageDetail['object_type'], 'M', true, true, CART_LANGUAGE);
                            $row = $record->{'Image'} = $imageData['detailed']['image_path'] ?? '';
                            $field = 'main_image';
                            $item->addChild($field, htmlspecialchars((string) ($row ?? '')));
                        } else {
                            throw new \Exception('Image should return 2 values: object_id and object_type');
                        }
                    }

                    // get attachment
                    if ($attachment = $column->getAttachment()) {

                        $attachmentDetail = $attachment($record);
                        if (!empty($attachmentDetail['object_id']) && !empty($attachmentDetail['object_type'])) {
                            $attachmentData = ['filename' => '']; //fn_get_attachments($attachmentDetail['object_type'], $attachmentDetail['object_id'], 'M', CART_LANGUAGE);
                            $row = $record->{'Attachments'} = implode('// ', array_column($attachmentData, 'filename')) ?? [];
                            $field = 'attachments';
                            $item->addChild($field, htmlspecialchars((string) ($row ?? '')));
                        } else {
                            throw new \Exception('Attachment should return 2 values: object_id and object_type');
                        }
                    }

                    $explode = explode('.', $column->getIndex());
                    $field = $explode[1] ?? $column->getIndex();
                    $item->addChild($field, htmlspecialchars((string) ($record->{$field} ?? '')));
                }
            }
        }

        echo $xml->asXML();
        exit;
    }
}
