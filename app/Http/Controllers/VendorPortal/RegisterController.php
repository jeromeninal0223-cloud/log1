<?php

namespace App\Http\Controllers\VendorPortal;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the vendor registration form
     */
    public function showRegistrationForm()
    {
        return view('VendorPortal.register');
    }

    /**
     * Handle vendor registration submission
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:vendors'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'company_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'business_type' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'business_license' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'tax_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'insurance_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'additional_documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle file uploads with custom naming
            $businessLicensePath = null;
            $taxCertificatePath = null;
            $insuranceCertificatePath = null;
            $additionalDocumentsPaths = [];

            // Clean company name for filename
            $cleanCompanyName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $request->company_name);
            $cleanCompanyName = preg_replace('/_+/', '_', $cleanCompanyName);
            $cleanCompanyName = trim($cleanCompanyName, '_');

            // Store business license
            if ($request->hasFile('business_license')) {
                $file = $request->file('business_license');
                $extension = $file->getClientOriginalExtension();
                $filename = $cleanCompanyName . '_Business_License.' . $extension;
                $businessLicensePath = $file->storeAs('vendor-documents/business-licenses', $filename, 'public');
            }

            // Store tax certificate
            if ($request->hasFile('tax_certificate')) {
                $file = $request->file('tax_certificate');
                $extension = $file->getClientOriginalExtension();
                $filename = $cleanCompanyName . '_Tax_Certificate.' . $extension;
                $taxCertificatePath = $file->storeAs('vendor-documents/tax-certificates', $filename, 'public');
            }

            // Store insurance certificate (optional)
            if ($request->hasFile('insurance_certificate')) {
                $file = $request->file('insurance_certificate');
                $extension = $file->getClientOriginalExtension();
                $filename = $cleanCompanyName . '_Insurance_Certificate.' . $extension;
                $insuranceCertificatePath = $file->storeAs('vendor-documents/insurance-certificates', $filename, 'public');
            }

            // Store additional documents (optional)
            if ($request->hasFile('additional_documents')) {
                foreach ($request->file('additional_documents') as $index => $file) {
                    $extension = $file->getClientOriginalExtension();
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $cleanOriginalName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $originalName);
                    $filename = $cleanCompanyName . '_Additional_' . ($index + 1) . '_' . $cleanOriginalName . '.' . $extension;
                    $additionalDocumentsPaths[] = $file->storeAs('vendor-documents/additional', $filename, 'public');
                }
            }

            // Create new vendor with 'Pending' status
            $vendor = Vendor::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'business_type' => $request->business_type,
                'address' => $request->address,
                'business_license_path' => $businessLicensePath,
                'tax_certificate_path' => $taxCertificatePath,
                'insurance_certificate_path' => $insuranceCertificatePath,
                'additional_documents_paths' => !empty($additionalDocumentsPaths) ? $additionalDocumentsPaths : null,
                'documents_verified' => false,
                'status' => 'Pending',
                'registered_at' => now(),
            ]);

            // Redirect to success page or login with success message
            return redirect()->route('vendor.login')
                ->with('success', '🎉 Registration successful! Your vendor account has been created and is pending approval. You will receive an email notification once your account is approved by our team. Thank you for choosing JetLouge Travels!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show vendor login form
     */
    public function showLoginForm()
    {
        return view('VendorPortal.login');
    }
}
