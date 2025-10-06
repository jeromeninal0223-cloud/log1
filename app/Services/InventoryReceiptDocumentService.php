<?php

namespace App\Services;

use App\Models\InventoryReceipt;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class InventoryReceiptDocumentService
{
    public function generateReceiptPDF(InventoryReceipt $receipt): string
    {
        try {
            \Log::info('Starting PDF generation for receipt', ['receipt_id' => $receipt->id, 'receipt_number' => $receipt->receipt_number]);
            
            // Configure Dompdf
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            
            $dompdf = new Dompdf($options);
            
            // Generate HTML content
            $html = $this->generateReceiptHTML($receipt);
            \Log::info('HTML content generated', ['html_length' => strlen($html)]);
            
            // Load HTML into Dompdf
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            
            // Render PDF
            $dompdf->render();
            \Log::info('PDF rendered successfully');
            
            // Generate filename
            $filename = 'inventory_receipt_' . $receipt->receipt_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $filePath = 'documents/inventory_receipts/' . $filename;
            
            // Ensure directory exists
            Storage::makeDirectory('documents/inventory_receipts');
            
            // Save PDF to storage
            Storage::put($filePath, $dompdf->output());
            \Log::info('PDF saved to storage', ['file_path' => $filePath]);
            
            return $filePath;
            
        } catch (\Exception $e) {
            \Log::error('PDF generation failed', [
                'receipt_id' => $receipt->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    private function generateReceiptHTML(InventoryReceipt $receipt): string
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Inventory Receipt - ' . htmlspecialchars($receipt->receipt_number ?: 'N/A', ENT_QUOTES, 'UTF-8') . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                    margin: 0;
                    padding: 20px;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #1e3a8a;
                    padding-bottom: 20px;
                }
                
                .company-logo {
                    font-size: 24px;
                    font-weight: bold;
                    color: #1e3a8a;
                    margin-bottom: 5px;
                }
                
                .document-title {
                    font-size: 18px;
                    font-weight: bold;
                    margin: 10px 0;
                    color: #1e3a8a;
                }
                
                .receipt-info {
                    display: table;
                    width: 100%;
                    margin-bottom: 20px;
                }
                
                .info-section {
                    display: table-cell;
                    width: 50%;
                    vertical-align: top;
                    padding-right: 20px;
                }
                
                .info-row {
                    margin-bottom: 8px;
                }
                
                .label {
                    font-weight: bold;
                    display: inline-block;
                    width: 140px;
                }
                
                .value {
                    color: #555;
                }
                
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                
                .items-table th,
                .items-table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                
                .items-table th {
                    background-color: #f8f9fa;
                    font-weight: bold;
                    color: #1e3a8a;
                }
                
                .items-table tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                
                .summary-section {
                    margin-top: 30px;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                
                .summary-row {
                    display: table;
                    width: 100%;
                    margin-bottom: 10px;
                }
                
                .summary-label {
                    display: table-cell;
                    width: 70%;
                    font-weight: bold;
                    text-align: right;
                    padding-right: 20px;
                }
                
                .summary-value {
                    display: table-cell;
                    width: 30%;
                    font-weight: bold;
                }
                
                .signatures {
                    margin-top: 50px;
                    text-align: center;
                    width: 100%;
                }
                
                .signature-section {
                    display: inline-block;
                    width: 300px;
                    text-align: center;
                    vertical-align: bottom;
                }
                
                .signature-line {
                    border-top: 1px solid #333;
                    margin-top: 40px;
                    padding-top: 5px;
                    font-weight: bold;
                }
                
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #666;
                    border-top: 1px solid #ddd;
{{ ... }}
                }
                
                .status-badge {
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 10px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                
                .status-completed {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
                
                .status-pending {
                    background-color: #fff3cd;
                    color: #856404;
                    border: 1px solid #ffeaa7;
                }
                
                .status-quality-issue {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="company-logo">JetLouge Travels</div>
                <div>Smart Warehousing System</div>
                <div class="document-title">INVENTORY RECEIPT</div>
            </div>
            
            <div class="receipt-info">
                <div class="info-section">
                    <div class="info-row">
                        <span class="label">Receipt Number:</span>
                        <span class="value">' . htmlspecialchars($receipt->receipt_number ?: 'N/A', ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Receipt Date:</span>
                        <span class="value">' . $receipt->receipt_date->format('F j, Y') . '</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Delivery Date:</span>
                        <span class="value">' . ($receipt->delivery_date ? $receipt->delivery_date->format('F j, Y') : 'N/A') . '</span>
                    </div>
                </div>
                <div class="info-section">
                    <div class="info-row">
                        <span class="label">Supplier:</span>
                        <span class="value">' . htmlspecialchars($receipt->supplier_name ?: 'Unknown Supplier', ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Purchase Order:</span>
                        <span class="value">' . htmlspecialchars($receipt->purchase_order_number ?: 'N/A', ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Invoice Number:</span>
                        <span class="value">' . htmlspecialchars($receipt->invoice_number ?: 'N/A', ENT_QUOTES, 'UTF-8') . '</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Generated:</span>
                        <span class="value">' . now()->format('F j, Y g:i A') . '</span>
                    </div>
                </div>
            </div>';
            
        // Add notes if available
        if ($receipt->notes) {
            $html .= '
            <div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #1e3a8a;">
                <strong>Notes:</strong><br>
                ' . nl2br(htmlspecialchars($receipt->notes)) . '
            </div>';
        }
        
        // Items table
        $html .= '
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Item Name</th>
                        <th style="width: 20%;">Description</th>
                        <th style="width: 8%;">Qty</th>
                        <th style="width: 8%;">Damaged</th>
                        <th style="width: 8%;">Unit</th>
                        <th style="width: 10%;">Unit Price</th>
                        <th style="width: 10%;">Total Price</th>
                        <th style="width: 6%;">Location</th>
                    </tr>
                </thead>
                <tbody>';
                
        $itemNumber = 1;
        $totalValue = 0;
        $totalQuantity = 0;
        $totalDamaged = 0;
        
        foreach ($receipt->items as $item) {
            $totalValue += $item->total_price;
            $totalQuantity += $item->quantity;
            $totalDamaged += $item->damaged_quantity;
            
            $html .= '
                <tr>
                    <td>' . $itemNumber++ . '</td>
                    <td><strong>' . htmlspecialchars($item->item_name ?: 'Unknown Item', ENT_QUOTES, 'UTF-8') . '</strong></td>
                    <td>' . htmlspecialchars($item->description ?: '-', ENT_QUOTES, 'UTF-8') . '</td>
                    <td style="text-align: center;">' . $item->quantity . '</td>
                    <td style="text-align: center; color: ' . ($item->damaged_quantity > 0 ? '#dc3545' : '#28a745') . ';">' . $item->damaged_quantity . '</td>
                    <td style="text-align: center;">' . $item->unit . '</td>
                    <td style="text-align: right;">PHP ' . number_format($item->unit_price, 2) . '</td>
                    <td style="text-align: right;">PHP ' . number_format($item->total_price, 2) . '</td>
                    <td style="text-align: center; font-size: 10px;">' . ucfirst(str_replace('_', ' ', $item->storage_location ?: 'Receiving Area')) . '</td>
                </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="summary-section">
                <div class="summary-row">
                    <div class="summary-label">Total Items:</div>
                    <div class="summary-value">' . $receipt->items->count() . '</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Total Quantity:</div>
                    <div class="summary-value">' . number_format($totalQuantity) . '</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Total Damaged:</div>
                    <div class="summary-value" style="color: ' . ($totalDamaged > 0 ? '#dc3545' : '#28a745') . ';">' . number_format($totalDamaged) . '</div>
                </div>
                <div class="summary-row" style="border-top: 2px solid #1e3a8a; padding-top: 10px; margin-top: 10px;">
                    <div class="summary-label">Total Value:</div>
                    <div class="summary-value" style="font-size: 16px; color: #1e3a8a;">PHP ' . number_format($totalValue, 2) . '</div>
                </div>
            </div>
            
            <div class="signatures">
                <div class="signature-section">
                    <div>Logistics Staff</div>
                    <div class="signature-line">' . htmlspecialchars($receipt->received_by ?: 'Logistics Staff', ENT_QUOTES, 'UTF-8') . '</div>
                </div>
            </div>
            
            <div class="footer">
                <p>This document was automatically generated by the Smart Warehousing System on ' . now()->format('F j, Y \a\t g:i A') . '</p>
                <p>JetLouge Travels - Inventory Receipt #' . $receipt->receipt_number . '</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
