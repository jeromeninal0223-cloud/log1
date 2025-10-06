<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Bid;
use App\Models\Vendor;
use App\Models\Opportunity;

class ContractTermsService
{
    /**
     * Generate comprehensive contract terms and conditions
     */
    public static function generateContractTerms(Contract $contract): string
    {
        $vendor = $contract->vendor;
        $bid = $contract->bid;
        $opportunity = $bid ? Opportunity::find($bid->opportunity_id) : null;
        
        $terms = self::getBaseContractTerms();
        
        // Get current authenticated user for procurement manager details
        $currentUser = auth()->user();
        $procurementManagerName = $currentUser ? $currentUser->name : 'Procurement Manager';
        $procurementManagerEmail = $currentUser ? $currentUser->email : 'procurement@jetlougetravels.com';
        $procurementManagerPhone = $currentUser->phone ?? '+63 917 123 4567';
        
        // Replace placeholders with actual data
        $replacements = [
            '[CONTRACT_NUMBER]' => $contract->contract_number,
            '[CONTRACT_VALUE]' => '₱' . number_format($contract->value, 2),
            '[VENDOR_NAME]' => $vendor->company_name ?? $vendor->name ?? 'Vendor',
            '[VENDOR_CONTACT_NAME]' => $vendor->name ?? 'Vendor Representative',
            '[VENDOR_COMPANY_NAME]' => $vendor->company_name ?? $vendor->name ?? 'Vendor Company',
            '[VENDOR_ADDRESS]' => $vendor->address ?? 'Address not provided',
            '[CONTRACT_TITLE]' => $contract->title,
            '[START_DATE]' => $contract->start_date ? $contract->start_date->format('F d, Y') : 'To be determined',
            '[END_DATE]' => $contract->end_date ? $contract->end_date->format('F d, Y') : 'To be determined',
            '[DELIVERY_TIMELINE]' => self::getDeliveryTimeline($bid),
            '[PAYMENT_TERMS]' => self::getPaymentTerms($contract->value, $bid),
            '[WARRANTY_PERIOD]' => self::getWarrantyPeriod($opportunity, $bid),
            '[COMPANY_NAME]' => 'Jetlouge Travels',
            '[CURRENT_DATE]' => now()->format('F d, Y'),
            '[VENDOR_SIGNATURE_SECTION]' => self::getVendorSignatureSection($contract),
            '[PROCUREMENT_SIGNATURE_SECTION]' => self::getProcurementSignatureSection($contract),
            '[PROCUREMENT_MANAGER_NAME]' => $procurementManagerName,
            '[PROCUREMENT_MANAGER_EMAIL]' => $procurementManagerEmail,
            '[PROCUREMENT_MANAGER_PHONE]' => $procurementManagerPhone,
        ];
        
        foreach ($replacements as $placeholder => $value) {
            $terms = str_replace($placeholder, $value, $terms);
        }
        
        return $terms;
    }
    
    /**
     * Get base contract terms template
     */
    private static function getBaseContractTerms(): string
    {
        return "
<div style='max-width: 800px; margin: 0 auto; padding: 40px; font-family: \"Times New Roman\", Times, serif; line-height: 1.8; color: #000; background: #fff; box-shadow: 0 0 20px rgba(0,0,0,0.1);'>

<!-- Document Header -->
<div style='text-align: center; margin-bottom: 50px; border-bottom: 3px double #000; padding-bottom: 30px;'>
<h1 style='font-size: 24px; font-weight: bold; letter-spacing: 2px; margin: 0 0 15px 0; text-transform: uppercase;'>CONTRACT AGREEMENT</h1>
<h2 style='font-size: 18px; font-weight: normal; margin: 0 0 10px 0;'>TERMS AND CONDITIONS</h2>
<p style='font-size: 14px; font-weight: bold; margin: 0; letter-spacing: 1px;'>Contract No: [CONTRACT_NUMBER]</p>
</div>

<!-- Parties Section -->
<div style='margin-bottom: 40px; text-align: justify;'>
<p style='text-indent: 50px; margin-bottom: 25px; font-size: 14px;'>
This Contract Agreement (\"Agreement\") is entered into on <strong>[CURRENT_DATE]</strong>, by and between <strong>[COMPANY_NAME]</strong>, a corporation organized and existing under the laws of the Philippines, with its principal place of business at [Company Address] (hereinafter referred to as the \"Company\" or \"Client\"), and <strong>[VENDOR_NAME]</strong>, with its principal place of business at <strong>[VENDOR_ADDRESS]</strong> (hereinafter referred to as the \"Contractor\" or \"Vendor\").
</p>

<p style='text-indent: 50px; margin-bottom: 25px; font-size: 14px;'>
<strong>WHEREAS</strong>, the Company desires to engage the Contractor to provide goods and/or services as described in \"<strong>[CONTRACT_TITLE]</strong>\"; and
</p>

<p style='text-indent: 50px; margin-bottom: 25px; font-size: 14px;'>
<strong>WHEREAS</strong>, the Contractor represents that it has the expertise, experience, and resources necessary to perform the services required under this Agreement;
</p>

<p style='text-indent: 50px; margin-bottom: 25px; font-size: 14px;'>
<strong>NOW, THEREFORE</strong>, in consideration of the mutual covenants and agreements contained herein, and for other good and valuable consideration, the receipt and sufficiency of which are hereby acknowledged, the parties agree as follows:
</p>
</div>

<!-- Letter Body - Terms and Conditions -->
<div style='margin-bottom: 30px; text-align: justify; font-size: 14px;'>

<h4 style='font-size: 16px; font-weight: bold; margin: 25px 0 15px 0; color: #2c5aa0; border-bottom: 1px solid #2c5aa0; padding-bottom: 5px;'>CONTRACT SCOPE AND SPECIFICATIONS</h4>

<p style='margin-bottom: 15px; text-indent: 30px;'>
This contract encompasses the provision of <strong>[CONTRACT_TITLE]</strong> with a total value of <strong>₱[CONTRACT_VALUE]</strong>. The contract period shall commence on <strong>[START_DATE]</strong> and conclude on <strong>[END_DATE]</strong>. All deliverables must meet or exceed the quality standards specified in your original bid proposal and comply with industry best practices.
</p>

<h4 style='font-size: 16px; font-weight: bold; margin: 25px 0 15px 0; color: #2c5aa0; border-bottom: 1px solid #2c5aa0; padding-bottom: 5px;'>DELIVERY AND PERFORMANCE REQUIREMENTS</h4>

<p style='margin-bottom: 15px; text-indent: 30px;'>
<strong>Delivery Schedule:</strong> [DELIVERY_TIMELINE]. Time is of the essence in this agreement, and adherence to the specified timeline is critical to our operations. We require regular progress updates and maintain open communication throughout the contract period to ensure seamless execution.
</p>

<p style='margin-bottom: 15px; text-indent: 30px;'>
All work must be performed in a professional and workmanlike manner in accordance with industry standards. Quality control inspections may be conducted at reasonable intervals to ensure compliance with our specifications.
</p>

<h4 style='font-size: 16px; font-weight: bold; margin: 25px 0 15px 0; color: #2c5aa0; border-bottom: 1px solid #2c5aa0; padding-bottom: 5px;'>PAYMENT TERMS AND CONDITIONS</h4>

<p style='margin-bottom: 15px; text-indent: 30px;'>
<strong>Payment Schedule:</strong> [PAYMENT_TERMS]. All invoices must be submitted with proper documentation, proof of delivery or completion, and any supporting materials as reasonably requested. Payment will be processed within thirty (30) days of receipt of valid invoices and acceptance of deliverables.
</p>

<p style='margin-bottom: 15px; text-indent: 30px;'>
Please note that all payments are subject to applicable taxes, withholding requirements, and statutory deductions as mandated by Philippine law.
</p>

<h4 style='font-size: 16px; font-weight: bold; margin: 25px 0 15px 0; color: #2c5aa0; border-bottom: 1px solid #2c5aa0; padding-bottom: 5px;'>WARRANTY AND QUALITY ASSURANCE</h4>

<p style='margin-bottom: 15px; text-indent: 30px;'>
<strong>Warranty Period:</strong> [WARRANTY_PERIOD]. You warrant that all goods and services will be free from defects in materials and workmanship and will conform to the specifications outlined in this agreement. Any defects or non-conformities discovered during the warranty period must be remedied at your expense promptly.
</p>

<h4 style='font-size: 16px; font-weight: bold; margin: 25px 0 15px 0; color: #2c5aa0; border-bottom: 1px solid #2c5aa0; padding-bottom: 5px;'>COMPLIANCE AND CONFIDENTIALITY</h4>

<p style='margin-bottom: 15px; text-indent: 30px;'>
You must comply with all applicable laws, regulations, and industry standards. All necessary permits, licenses, and certifications must be obtained and maintained throughout the contract period. Both parties acknowledge the confidential nature of business information and agree to maintain strict confidentiality regarding proprietary processes, client data, and business operations.
</p>

<h4 style='font-size: 16px; font-weight: bold; margin: 25px 0 15px 0; color: #2c5aa0; border-bottom: 1px solid #2c5aa0; padding-bottom: 5px;'>GENERAL PROVISIONS</h4>

<p style='margin-bottom: 15px; text-indent: 30px;'>
This agreement constitutes the entire understanding between our organizations and supersedes all prior negotiations or agreements. Any modifications must be made in writing and signed by authorized representatives of both parties. In the event of disputes, we shall first attempt resolution through good faith negotiations, followed by mediation if necessary.
</p>

<p style='margin-bottom: 15px; text-indent: 30px;'>
For contracts exceeding ₱500,000.00, a performance bond or bank guarantee equal to ten percent (10%) of the contract value may be required. This agreement shall be governed by the laws of the Republic of the Philippines.
</p>

</div>

<!-- Professional Letter Closing -->
<div style='margin-top: 40px; margin-bottom: 30px; font-size: 14px;'>

<p style='margin-bottom: 20px; text-indent: 30px; text-align: justify;'>
We trust that these terms are acceptable and look forward to a successful business partnership. Should you have any questions or require clarification on any aspect of this agreement, please do not hesitate to contact our contracts department.
</p>

<p style='margin-bottom: 30px; text-indent: 30px;'>
Please confirm your acceptance of these terms by signing and returning a copy of this letter within seven (7) business days from the date hereof.
</p>

<p style='margin-bottom: 40px;'>
Yours sincerely,
</p>

<div style='margin-bottom: 50px;'>
<div style='border-bottom: 2px solid #000; width: 300px; margin-bottom: 10px;'></div>
<p style='margin: 0; font-weight: bold;'>[PROCUREMENT_MANAGER_NAME]</p>
<p style='margin: 0; font-size: 13px;'>Procurement Manager</p>
<p style='margin: 0; font-size: 13px;'>Jetlouge Travels</p>
<p style='margin: 0; font-size: 13px;'>Email: [PROCUREMENT_MANAGER_EMAIL]</p>
<p style='margin: 0; font-size: 13px;'>Phone: [PROCUREMENT_MANAGER_PHONE]</p>
</div>

</div>

<!-- Acceptance Section -->
<div style='margin-top: 50px; border-top: 1px solid #ccc; padding-top: 30px; page-break-inside: avoid; break-inside: avoid;'>
<h4 style='font-size: 16px; font-weight: bold; margin-bottom: 20px; color: #2c5aa0;'>CONTRACTOR ACCEPTANCE</h4>

<p style='margin-bottom: 20px; font-size: 14px;'>
I/We acknowledge receipt of this contract terms letter and hereby accept all terms and conditions as outlined above.
</p>

<div style='margin-top: 40px; page-break-inside: avoid; break-inside: avoid;'>
<div style='width: 100%;'>
[VENDOR_SIGNATURE_SECTION]
<p style='margin: 10px 0 0 0; font-size: 13px;'><strong>Signed by [VENDOR_CONTACT_NAME] for and behalf of [VENDOR_COMPANY_NAME]</strong></p>
</div>
</div>


<!-- Procurement Signature Section -->
<div style='margin-top: 50px; border-top: 1px solid #ccc; padding-top: 30px; page-break-inside: avoid; break-inside: avoid;'>
<h4 style='font-size: 16px; font-weight: bold; margin-bottom: 20px; color: #2c5aa0;'>PROCUREMENT APPROVAL</h4>

<p style='margin-bottom: 20px; font-size: 14px;'>
This contract has been reviewed and approved by the procurement department.
</p>

<div style='margin-top: 40px; page-break-inside: avoid; break-inside: avoid;'>
<div style='width: 100%;'>
[PROCUREMENT_SIGNATURE_SECTION]
<p style='margin: 10px 0 0 0; font-size: 13px;'><strong>Signed by [PROCUREMENT_MANAGER_NAME] for and behalf of [COMPANY_NAME]</strong></p>
</div>
</div>
</div>

</div>

<!-- Letter Footer -->
<div style='margin-top: 50px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ccc; padding-top: 20px;'>
<p style='margin: 0;'>This contract terms letter is confidential and proprietary to Jetlouge Travels.</p>
<p style='margin: 5px 0 0 0;'>Please retain this document for your records and reference.</p>
</div>

</div>

</div>
        ";
    }
    
    /**
     * Get delivery timeline based on bid information
     */
    private static function getDeliveryTimeline($bid): string
    {
        if (!$bid) {
            return "To be determined based on project requirements";
        }
        
        // Check if bid has completion date
        if ($bid->completion_date) {
            $days = (int) ceil(now()->diffInDays($bid->completion_date, false));
            // Ensure we have at least 1 day
            $days = max(1, $days);
            return "Delivery within {$days} calendar days from contract signing";
        }
        
        // Default timeline based on contract value
        $amount = $bid->amount ?? 0;
        if ($amount > 1000000) {
            return "Delivery within 60 calendar days from contract signing";
        } elseif ($amount > 500000) {
            return "Delivery within 45 calendar days from contract signing";
        } elseif ($amount > 100000) {
            return "Delivery within 30 calendar days from contract signing";
        } else {
            return "Delivery within 15 calendar days from contract signing";
        }
    }
    
    /**
     * Get payment terms based on bid data or contract value
     */
    private static function getPaymentTerms($value, $bid = null): string
    {
        // Use payment terms from bid if available
        if ($bid && !empty($bid->payment_terms_details)) {
            return $bid->payment_terms_details;
        }
        
        // Fallback to default terms based on contract value
        if ($value > 1000000) {
            return "30% advance payment, 40% upon 50% completion, 30% upon final delivery and acceptance";
        } elseif ($value > 500000) {
            return "20% advance payment, 50% upon delivery, 30% upon final acceptance";
        } elseif ($value > 100000) {
            return "100% payment within 30 days of delivery and acceptance";
        } else {
            return "100% payment within 15 days of delivery and acceptance";
        }
    }
    
    /**
     * Get warranty period based on bid data or opportunity type
     */
    private static function getWarrantyPeriod($opportunity, $bid = null): string
    {
        // Use warranty period from bid if available
        if ($bid && !empty($bid->warranty_period)) {
            $warrantyPeriod = $bid->warranty_period;
            
            // Handle custom warranty
            if ($warrantyPeriod === 'custom' && !empty($bid->custom_warranty)) {
                return $bid->custom_warranty . " from date of delivery/completion";
            }
            
            // Convert standard warranty periods to readable format
            $warrantyMap = [
                '3_months' => '3 months',
                '6_months' => '6 months', 
                '12_months' => '12 months',
                '18_months' => '18 months',
                '24_months' => '24 months',
                '36_months' => '36 months'
            ];
            
            if (isset($warrantyMap[$warrantyPeriod])) {
                return $warrantyMap[$warrantyPeriod] . " from date of delivery/completion";
            }
        }
        
        // Fallback to default warranty based on opportunity category
        if (!$opportunity) {
            return "12 months from date of delivery/completion";
        }
        
        $category = strtolower($opportunity->category ?? '');
        
        if (str_contains($category, 'equipment') || str_contains($category, 'hardware')) {
            return "24 months from date of delivery for equipment, 12 months for installation services";
        } elseif (str_contains($category, 'software') || str_contains($category, 'system')) {
            return "12 months from date of delivery with free bug fixes and minor updates";
        } elseif (str_contains($category, 'construction') || str_contains($category, 'infrastructure')) {
            return "36 months from date of completion for structural work, 12 months for other components";
        } elseif (str_contains($category, 'service') || str_contains($category, 'maintenance')) {
            return "Service level agreement as specified, with 90-day warranty on completed work";
        } else {
            return "12 months from date of delivery/completion";
        }
    }

    /**
     * Get vendor signature section with actual signature if signed
     */
    private static function getVendorSignatureSection(Contract $contract): string
    {
        if ($contract->vendor_signed_at) {
            $signatureImage = '';
            if ($contract->vendor_signature_image) {
                // Add data:image/png;base64, prefix if not already present
                $imageData = $contract->vendor_signature_image;
                if (strpos($imageData, 'data:image') !== 0) {
                    $imageData = 'data:image/png;base64,' . $imageData;
                }
                $signatureImage = "<div style='text-align: center; margin: 15px 0; page-break-inside: avoid;'>
                    <img src='{$imageData}' style='max-width: 180px; max-height: 80px; border: 1px solid #28a745; display: block; margin: 0 auto;' alt='Vendor Signature' />
                </div>";
            }
            
            return "
<div style='background-color: #e8f5e8; border: 2px solid #28a745; padding: 15px; margin-bottom: 20px; border-radius: 5px; page-break-inside: avoid; break-inside: avoid;'>
    <div style='text-align: center; color: #28a745; font-weight: bold; font-size: 16px; margin-bottom: 10px;'>
        ✓ DIGITALLY SIGNED
    </div>
    {$signatureImage}
    <div style='font-size: 12px; color: #155724; text-align: center; margin-top: 10px;'>
        <strong>Signed on:</strong> " . $contract->vendor_signed_at->format('F d, Y \a\t g:i A') . "
    </div>
</div>";
        } else {
            return "<div style='border-bottom: 2px solid #000; margin-bottom: 10px; height: 40px; background-color: #f8f9fa; border-style: dashed; border-color: #ccc;'></div>";
        }
    }

    /**
     * Get vendor signature date for the separate date field
     */
    private static function getVendorSignatureDate(Contract $contract): string
    {
        if ($contract->vendor_signed_at) {
            return $contract->vendor_signed_at->format('M d, Y');
        }
        return '';
    }

    /**
     * Get procurement signature section with actual signature if signed
     */
    private static function getProcurementSignatureSection(Contract $contract): string
    {
        if ($contract->procurement_signed_at) {
            $signatureImage = '';
            if ($contract->procurement_signature_image) {
                // Add data:image/png;base64, prefix if not already present
                $imageData = $contract->procurement_signature_image;
                if (strpos($imageData, 'data:image') !== 0) {
                    $imageData = 'data:image/png;base64,' . $imageData;
                }
                $signatureImage = "<div style='text-align: center; margin: 15px 0; page-break-inside: avoid;'>
                    <img src='{$imageData}' style='max-width: 180px; max-height: 80px; border: 1px solid #2196f3; display: block; margin: 0 auto;' alt='Procurement Signature' />
                </div>";
            }
            
            return "
<div style='background-color: #e3f2fd; border: 2px solid #2196f3; padding: 15px; margin-bottom: 20px; border-radius: 5px; page-break-inside: avoid; break-inside: avoid;'>
    <div style='text-align: center; color: #2196f3; font-weight: bold; font-size: 16px; margin-bottom: 10px;'>
        ✓ DIGITALLY SIGNED
    </div>
    {$signatureImage}
    <div style='font-size: 12px; color: #0d47a1; text-align: center; margin-top: 10px;'>
        <strong>Signed on:</strong> " . $contract->procurement_signed_at->format('F d, Y \a\t g:i A') . "
    </div>
</div>";
        } else {
            return "<div style='border-bottom: 2px solid #000; margin-bottom: 10px; height: 40px; background-color: #f8f9fa; border-style: dashed; border-color: #ccc;'></div>";
        }
    }

    /**
     * Get procurement signature date for the separate date field
     */
    private static function getProcurementSignatureDate(Contract $contract): string
    {
        if ($contract->procurement_signed_at) {
            return $contract->procurement_signed_at->format('M d, Y');
        }
        return '';
    }
}
