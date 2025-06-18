<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\StockCard;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\MedicalRecord;
use App\Models\ChartOfAccount;
use App\Models\DentalMaterial;

class MedicalRecordController extends Controller
{

    public function list()
    {
        $reservations = MedicalRecord::all(); // Ambil semua data reservasi
        $reservations = MedicalRecord::with('patient')->get();
        return view('dashboard.reservations.index', [
            'title' => 'Data Reservasi',
            'reservations' => $reservations
        ]);
    }

    public function selectIncomplete()
    {
        // dd('hai');
        $records = MedicalRecord::with(['patient', 'doctor'])
            ->where(function ($query) {
                $query->whereNull('teeth_condition')
                    ->orDoesntHave('procedures');
            })
            ->orderBy('tanggal_reservasi', 'asc')
            ->get();

        return view('dashboard.medical_records.selectmedicalrecord', compact('records'));
    }


    public function sendWhatsApp($id)
    {
        // Ambil data reservasi berdasarkan ID
        $reservation = MedicalRecord::with('patient')->findOrFail($id);

        // Nomor telepon pasien
        $phoneNumber = $reservation->patient->home_mobile;

        // Pesan template
        $message = "Halo {$reservation->patient->fname} {$reservation->patient->mname} {$reservation->patient->lname}, Selamat datang di klinik gigi Senyumqu. Untuk konfirmasi kehadiran konsultasi di tanggal {$reservation->tanggal_reservasi} pukul {$reservation->jam_mulai} ya. Terima kasih! -Salam Admin Senyumqu";

        // Halo Jocelyn, Selamat datang di klinik Senyumqu. Untuk konfirmasi kehadiran konsultasi tanggal 2025-05-16 pukul 11:00 ya. Terima kasih! -Salam Admin Senyumqu

        // Redirect ke wa.me dengan pesan template
        return redirect("https://wa.me/{$phoneNumber}?text=" . urlencode($message));
    }

    public function waConfirmation($id)
    {
        // dd('oi');
        $reservation = MedicalRecord::findOrFail($id);
        $reservation->status_konfirmasi = 'Sudah Dikonfirmasi';
        $reservation->save();
        return redirect()->back()->with('success', 'Reservation confirmed successfully!');
    }

    //     public function updateConfirmation($id)
    // {
    //     $reservation = MedicalRecord::findOrFail($id);

    //     $action = request('action');

    //     if ($action === 'confirm') {
    //         $reservation->confirmation_status = 'confirmed';
    //         $reservation->confirmed_at = now();
    //         $message = 'Reservation confirmed successfully!';
    //     } elseif ($action === 'cancel') {
    //         $reservation->confirmation_status = 'cancelled';
    //         $message = 'Reservation cancelled successfully!';
    //     }

    //     $reservation->save();

    //     return back()->with('success', $message);
    // }

    public function index($patientId)
    {
        // Mengambil rekam medis langsung berdasarkan patient_id
        $medicalRecords = MedicalRecord::with(['procedures'])
            ->where('patient_id', $patientId)
            ->latest()
            ->get();

        $patient = Patient::findOrFail($patientId);
        $patientName = trim("{$patient->fname} {$patient->mname} {$patient->lname}");
        $proceduress = Procedure::all();

        return view('dashboard.medical_records.index', compact('medicalRecords', 'patientId', 'patientName', 'proceduress'));
    }


    // public function create(Request $request, $patientId)
    // {
    //     // Mengambil data pasien berdasarkan patientId
    //     $patient = Patient::findOrFail($patientId);

    //     // Mengambil semua reservasi yang dimiliki oleh pasien tertentu. 
    //     $reservations = Reservation::where('patient_id', $patientId)->whereDoesntHave('medicalRecord')->get();

    //     // Mengambil semua prosedur yang tersedia
    //     $procedures = Procedure::all();

    //     // Mengambil prosedur yang dipilih (jika ada)
    //     $selectedProcedureIds = $request->input('procedure_id', []);

    //     // Mengumpulkan bahan dental yang terkait dengan prosedur yang dipilih
    //     // Mengumpulkan bahan dental yang terkait dengan prosedur yang dipilih
    //     $selectedMaterials = [];
    //     if (!empty($selectedProcedureIds)) {
    //         $selectedProcedures = Procedure::whereIn('id', $selectedProcedureIds)->with('dentalMaterials')->get();

    //         foreach ($selectedProcedures as $procedure) {
    //             foreach ($procedure->dentalMaterials as $material) {
    //                 if (!isset($selectedMaterials[$material->id])) {
    //                     $selectedMaterials[$material->id] = [
    //                         'name' => $material->name,
    //                         'quantity' => $material->pivot->quantity, // ❌ Masih menggunakan pivot dari `medical_record_dental_material`
    //                     ];
    //                 } else {
    //                     $selectedMaterials[$material->id]['quantity'] += $material->pivot->quantity;
    //                 }
    //             }
    //         }
    //     }


    //     // Mengirim data ke view create
    //     return view('dashboard.medical_records.create', [
    //         'patientName' => $patient->name,
    //         'patientId' => $patientId,
    //         'reservations' => $reservations,
    //         'procedures' => $procedures,
    //         'selectedMaterials' => $selectedMaterials,
    //     ]);
    // }

    // public function store(Request $request, $patientId)
    // {
    //     $validatedData = $request->validate([
    //         'reservation_id' => 'required|exists:reservations,id',
    //         'procedure_id' => 'required|array',
    //         'procedure_id.*' => 'exists:procedures,id',
    //         'teeth_condition' => 'required|string',
    //         'tooth_numbers' => 'nullable|array',
    //         'procedure_notes' => 'nullable|array',
    //     ]);

    //     // Temukan reservasi pasien
    //     $reservation = Reservation::findOrFail($validatedData['reservation_id']);

    //     // Simpan rekam medis
    //     $medicalRecord = new MedicalRecord();
    //     $medicalRecord->reservation_id = $reservation->id;
    //     $medicalRecord->teeth_condition = $validatedData['teeth_condition'];
    //     $medicalRecord->save();

    //     // Ambil daftar prosedur yang memerlukan nomor gigi
    //     $proceduresRequiringTeeth = Procedure::where('requires_tooth', 1)->pluck('id')->toArray();
    //     $uniqueCombinations = [];

    //     foreach ($validatedData['procedure_id'] as $procedureId) {
    //         if (in_array($procedureId, $proceduresRequiringTeeth)) {
    //             // Jika prosedur memerlukan nomor gigi, pastikan ada data
    //             if (!isset($validatedData['tooth_numbers'][$procedureId]) || !is_array($validatedData['tooth_numbers'][$procedureId])) {
    //                 return redirect()->back()->with('error', "Tooth number is required for procedure ID: $procedureId");
    //             }

    //             foreach ($validatedData['tooth_numbers'][$procedureId] as $toothNumber) {
    //                 $procedureNotes = $validatedData['procedure_notes'][$procedureId][$toothNumber] ?? null;

    //                 $combinationKey = $procedureId . '-' . $toothNumber;
    //                 if (!in_array($combinationKey, $uniqueCombinations)) {
    //                     $uniqueCombinations[] = $combinationKey;

    //                     $medicalRecord->procedures()->attach($procedureId, [
    //                         'tooth_number' => $toothNumber,
    //                         'notes' => $procedureNotes,
    //                     ]);
    //                 }
    //             }
    //         } else {
    //             // Jika prosedur tidak membutuhkan gigi, cukup simpan prosedurnya dengan notes (jika ada)
    //             $procedureNotes = $validatedData['procedure_notes'][$procedureId] ?? null;

    //             if (!in_array($procedureId, $uniqueCombinations)) {
    //                 $uniqueCombinations[] = $procedureId;

    //                 $medicalRecord->procedures()->attach($procedureId, [
    //                     'tooth_number' => null, // Tidak perlu gigi
    //                     'notes' => is_array($procedureNotes) ? implode(', ', $procedureNotes) : $procedureNotes,
    //                 ]);
    //             }
    //         }
    //     }

    //     return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
    //         ->with('success', 'Medical Record and Odontogram have been saved successfully.');
    // }

    public function selectMaterials($medicalRecordId)
    {
        $medicalRecord = MedicalRecord::with('patient', 'procedures.dentalMaterials')->findOrFail($medicalRecordId);
        // Jika relasi 'procedures' kosong, hentikan proses dan redirect.
        if ($medicalRecord->procedures->isEmpty()) {
            return redirect()->back()->with('error', 'Unable to select material. The doctor has not added any procedures to this medical record.');
        }
        $procedures = $medicalRecord->procedures;

        $dentalMaterialIds = $procedures->flatMap->dentalMaterials->pluck('id')->unique();

        $stockCards = StockCard::select('dental_material_id', 'remaining_stock', 'average_price')
            ->whereIn('dental_material_id', $dentalMaterialIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('dental_material_id');

        $materials = [];

        foreach ($procedures as $procedure) {
            foreach ($procedure->dentalMaterials as $material) {
                if (!isset($materials[$material->id])) {
                    $stock = $stockCards->firstWhere('dental_material_id', $material->id);

                    $materials[$material->id] = [
                        'name' => $material->name,
                        'unit_type' => $material->unit_type,
                        'stock_quantity' => $stock ? $stock->remaining_stock : 0,
                        'average_price' => $stock ? $stock->average_price : 0,
                        'quantity' => $material->pivot->quantity,
                        'procedure_id' => $procedure->id,
                    ];
                } else {
                    $materials[$material->id]['quantity'] += $material->pivot->quantity;
                }
            }
        }

        // Ambil semua bahan untuk dropdown tambahan
        $allMaterials = DentalMaterial::all()->map(function ($material) {
            $latestStock = StockCard::where('dental_material_id', $material->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $material->stock_quantity = $latestStock ? $latestStock->remaining_stock : 0;
            return $material;
        });

        $patientName = trim("{$medicalRecord->patient->fname} {$medicalRecord->patient->mname} {$medicalRecord->patient->lname}");

        return view('dashboard.medical_records.selectMaterials', [
            'medicalRecordId' => $medicalRecordId,
            'procedures' => $procedures,
            'materials' => $materials,
            'allMaterials' => $allMaterials,
            'patientName' => $patientName ?? 'Unknown',
            'patientId' => $medicalRecord->patient->id ?? 0,
        ]);
    }

    // public function saveMaterials(Request $request, $medicalRecordId)
    // {
    //     $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

    //     // Validasi input bahan dan kuantitas
    //     $validatedData = $request->validate([
    //         'quantities' => 'required|array',
    //     ]);

    //     $totalHPP = 0;

    //     foreach ($validatedData['quantities'] as $materialId => $quantity) {
    //         $quantity = (int) $quantity; // Konversi ke integer

    //         if ($quantity > 0) {
    //             $material = DentalMaterial::findOrFail($materialId);

    //             // Debugging stok sebelum update
    //             // dd('Sebelum Penyimpanan', $materialId, $quantity, $material->stock_quantity);

    //             if ($material->stock_quantity < $quantity) {
    //                 return redirect()->back()->with('error', 'Not enough stock for ' . $material->name);
    //             }

    //             // Kurangi stok bahan
    //             $material->stock_quantity -= $quantity;
    //             $material->save();

    //             // Simpan hubungan antara rekam medis dan bahan dengan syncWithoutDetaching
    //             $medicalRecord->dentalMaterials()->syncWithoutDetaching([
    //                 $materialId => ['quantity' => $quantity]
    //             ]);

    //             // Hitung HPP
    //             $materialHPP = $quantity * $material->unit_price;
    //             $totalHPP += $materialHPP;

    //             // Debugging setelah penyimpanan
    //             // dd('Setelah Penyimpanan', $medicalRecord->dentalMaterials()->get());
    //         }
    //     }

    //     $transactionId = Transaction::where('medical_record_id', $medicalRecord->id)->value('id');


    //     if ($totalHPP > 0) {
    //         // 1. Buat Journal Entry
    //         $journalEntry = new JournalEntry();
    //         $journalEntry->transaction_id = $transactionId; // Gunakan transaction_id dinamis
    //         $journalEntry->entry_date = now();
    //         $journalEntry->description = 'HPP untuk Prosedur pada Medical Record ' . $medicalRecord->id;
    //         $journalEntry->save();

    //         // 2. Debit HPP Bahan Dental
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => 20, // ID COA HPP Bahan Dental
    //             'debit' => $totalHPP,
    //             'credit' => 0
    //         ]);

    //         // 3. Kredit Persediaan Bahan Dental
    //         JournalDetail::create([
    //             'journal_entry_id' => $journalEntry->id,
    //             'coa_id' => 13, // ID COA Persediaan Bahan Dental
    //             'debit' => 0,
    //             'credit' => $totalHPP
    //         ]);
    //     }
    //     return redirect()->route('dashboard.medical_records.index', ['patientId' => $medicalRecord->reservation->patient_id])
    //         ->with('success', 'Dental materials have been successfully saved.');
    // }

    public function showMaterials($medicalRecordId)
    {
        $referenceNumber = 'MR-' . $medicalRecordId;

        $materialsUsed = StockCard::with('material') // pastikan relasi 'material' ada
            ->where('reference_number', $referenceNumber)
            ->where('type', 'usage')
            ->get();

        $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);
        $patient = $medicalRecord->patient;

        return view('dashboard.medical_records.showMaterials', compact('medicalRecord', 'materialsUsed', 'patient'));
    }

    public function saveMaterials(Request $request, $medicalRecordId)
    {
        // dd('a');
        try {
            $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

            // Validasi input bahan dan kuantitas
            $validatedData = $request->validate([
                'quantities' => 'nullable|array',
                'extra_materials' => 'array',
                'extra_materials.*.material_id' => 'nullable|exists:dental_materials,id',
                'extra_materials.*.selected_quantity' => 'nullable|numeric|min:0',
            ]);

            // dd($validatedData);

            $totalHPP = 0;

            // dd($validatedData['quantities']);
            if (!empty($validatedData['quantities'])) {
                foreach ($validatedData['quantities'] as $materialId => $quantity) {
                    $quantity = (int) $quantity; // Konversi ke integer

                    // dd($quantity);

                    if ($quantity > 0) {
                        $material = DentalMaterial::findOrFail($materialId);
                        // dd('ada');

                        // Ambil data stok terakhir dari kartu stok
                        $latestStock = StockCard::where('dental_material_id', $materialId)
                            ->latest('created_at')
                            ->first();

                        if (!$latestStock || $latestStock->remaining_stock < $quantity) {
                            return redirect()->back()->with('error', 'Not enough stock for ' . $material->name);
                        }

                        // Kurangi stok di kartu stok
                        $newStock = $latestStock->remaining_stock - $quantity;
                        $hppPrice = $latestStock->average_price; // Harga per unit berdasarkan rata-rata

                        // Simpan ke kartu stok
                        StockCard::create([
                            'dental_material_id' => $materialId,
                            'date' => now(),
                            'reference_number' => 'MR-' . $medicalRecordId, // Nomor referensi dari rekam medis
                            'price_out' => $hppPrice,
                            'quantity_out' => $quantity,
                            'remaining_stock' => $newStock,
                            'average_price' => $latestStock->average_price, // Harga tetap sama
                            'type' => 'usage'
                        ]);

                        // dd('saved');

                        // if ($material->stock_quantity < $quantity) {
                        //     return redirect()->back()->with('error', 'Not enough stock for ' . $material->name);
                        // }

                        // Hitung HPP
                        $materialHPP = $quantity * $hppPrice;
                        $totalHPP += $materialHPP;
                    }
                }
            }
            // dd('saved');
            // dd($totalHPP);

            if (!empty($validatedData['extra_materials'])) {
                // dd('ada extra material');
                foreach ($validatedData['extra_materials'] as $extra) {
                    $materialId = $extra['material_id'] ?? null;
                    $quantity = isset($extra['selected_quantity']) ? (float) $extra['selected_quantity'] : 0;

                    if ($materialId && $quantity > 0) {
                        $material = DentalMaterial::findOrFail($materialId);

                        $latestStock = StockCard::where('dental_material_id', $materialId)
                            ->latest('created_at')
                            ->first();

                        // dd($latestStock->remaining_stock);

                        if (!$latestStock || $latestStock->remaining_stock < $quantity) {
                            return redirect()->back()->with('error', 'Not enough stock for ' . $material->name);
                        }

                        $newStock = $latestStock->remaining_stock - $quantity;
                        $hppPrice = $latestStock->average_price;

                        StockCard::create([
                            'dental_material_id' => $materialId,
                            'date' => now(),
                            'reference_number' => 'MR-' . $medicalRecordId,
                            'price_out' => $hppPrice,
                            'quantity_out' => $quantity,
                            'remaining_stock' => $newStock,
                            'average_price' => $hppPrice,
                            'type' => 'usage'
                        ]);

                        $totalHPP += $quantity * $hppPrice;
                    }
                }
            }

            // $transactionId = Transaction::where('medical_record_id', $medicalRecord->id)->value('id');

            // dd($transactionId);
            // dd('s');
            // dd($totalHPP);

            if ($totalHPP > 0) {
                // dd('mau buat jurnal');
                // 1. Buat Journal Entry
                $journalEntry = new JournalEntry();
                $journalEntry->medical_record_id = $medicalRecord->id;
                $journalEntry->entry_date = now();
                $journalEntry->description = 'HPP untuk Prosedur pada Medical Record ' . $medicalRecord->id;
                $journalEntry->save();

                // dd('journal saved');
                $idBahanDental = ChartOfAccount::where('name', 'HPP Bahan Dental')->value('id');
                $idPersediaanBahanDental = ChartOfAccount::where('name', 'Persediaan Barang Medis')->value('id');

                // dd($idBahanDental, $idPersediaanBahanDental);

                // 2. Debit HPP Bahan Dental
                JournalDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    // 'coa_id' => 20, // ID COA HPP Bahan Dental
                    'coa_id' => $idBahanDental, // ID COA HPP Bahan Dental
                    'debit' => $totalHPP,
                    'credit' => 0
                ]);

                // 3. Kredit Persediaan Bahan Dental
                JournalDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    // 'coa_id' => 13, // ID COA Persediaan Bahan Dental
                    'coa_id' => $idPersediaanBahanDental,
                    'debit' => 0,
                    'credit' => $totalHPP
                ]);
            }
            return redirect()->route('dashboard.medical_records.index', ['patientId' => $medicalRecord->patient_id])
                ->with('success', 'Dental materials have been successfully saved.');
        } catch (Exception $e) {
            return redirect()->route('dashboard.medical_records.index', ['patientId' => $medicalRecord->patient_id])
                ->with('error', 'Failed to save dental materials. Please try again.');
        }
    }


    public function removeMaterial($medicalRecordId, $materialId)
    {
        $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

        // Ambil jumlah bahan yang digunakan
        $materialUsage = $medicalRecord->dentalMaterials()->where('dental_material_id', $materialId)->first();

        if ($materialUsage) {
            $quantityUsed = $materialUsage->pivot->quantity;

            // Ambil data stok terakhir
            $latestStock = StockCard::where('dental_material_id', $materialId)
                ->latest('date')
                ->first();

            if ($latestStock) {
                // Tambah stok kembali
                $newStock = $latestStock->remaining_stock + $quantityUsed;

                // Simpan ke kartu stok
                StockCard::create([
                    'dental_material_id' => $materialId,
                    'date' => now(),
                    'reference_number' => 'MR-' . $medicalRecordId . '-REMOVED',
                    'price_in' => $latestStock->average_price,
                    'quantity_in' => $quantityUsed,
                    'remaining_stock' => $newStock,
                    'average_price' => $latestStock->average_price, // Harga tetap sama
                ]);
            }
        }

        return redirect()->route('dashboard.medical_records.selectMaterials', ['medicalRecordId' => $medicalRecordId])
            ->with('success', 'Dental material removed successfully.');
    }


    public function procedureMaterialsPage()
    {
        // Ambil semua prosedur beserta bahan materialnya
        $procedures = Procedure::with('dentalMaterials')->get();

        // Kirim data ke view
        return view('dashboard.procedure_materials', compact('procedures'));
    }

    public function edit($patientId, $recordId)
    {
        $medicalRecord = MedicalRecord::with(['procedures'])->findOrFail($recordId);

        // Ambil semua prosedur yang tersedia
        $procedures = Procedure::all();

        $selectedProcedures = $medicalRecord->procedures->pluck('id')->toArray();

        // Ambil semua nomor gigi yang terkait dengan rekam medis ini
        $medicalRecordProcedure = $medicalRecord->procedures->map(function ($po) {
            return [
                'procedure_id' => $po->procedure_id,
                'tooth_number' => $po->tooth_number,
                'notes' => $po->notes,
            ];
        });

        return view('dashboard.medical_records.edit', compact(
            'medicalRecord',
            'patientId',
            'procedures',
            'selectedProcedures',
            'medicalRecordProcedure'
        ));
    }

    public function update(Request $request, $patientId, $recordId)
    {
        $updated_by = auth()->id();
        $validatedData = $request->validate([
            'teeth_condition' => 'required|string',
            'subjective' => 'required|string',
            'objective' => 'required|string',
            'assessment' => 'required|string',
            'plan' => 'required|string',
            'procedure_id' => 'required|array',
            'procedure_id.*' => 'exists:procedures,id',
            'teeth_condition' => 'required|string',
            'tooth_numbers' => 'nullable|array',
            'procedure_notes' => 'nullable|array',
            'procedure_surface' => 'nullable|array',
        ]);

        // dd($validatedData);

        // Ambil rekam medis berdasarkan ID
        $medicalRecord = MedicalRecord::findOrFail($recordId);

        // Update kondisi gigi pada rekam medis
        $medicalRecord->update(['teeth_condition' => $validatedData['teeth_condition']]);

        $medicalRecord->update(['subjective' => $validatedData['subjective']]);
        $medicalRecord->update(['objective' => $validatedData['objective']]);
        $medicalRecord->update(['assessment' => $validatedData['assessment']]);
        $medicalRecord->update(['plan' => $validatedData['plan']]);

        // Ambil daftar prosedur yang memerlukan nomor gigi
        $proceduresRequiringTeeth = Procedure::where('requires_tooth', 1)->pluck('id')->toArray();
        $uniqueCombinations = [];

        foreach ($validatedData['procedure_id'] as $procedureId) {
            if (in_array($procedureId, $proceduresRequiringTeeth)) {
                // Validasi bahwa ada nomor gigi
                if (!isset($validatedData['tooth_numbers'][$procedureId]) || !is_array($validatedData['tooth_numbers'][$procedureId])) {
                    return redirect()->back()->with('error', "Tooth number is required for procedure ID: $procedureId");
                }

                foreach ($validatedData['tooth_numbers'][$procedureId] as $toothNumber) {
                    $procedureNotes = $validatedData['procedure_notes'][$procedureId][$toothNumber] ?? null;
                    $procedureSurfaceArray = $validatedData['procedure_surface'][$procedureId][$toothNumber] ?? [];

                    // Gabungkan array permukaan menjadi string, jika ada
                    $procedureSurface = is_array($procedureSurfaceArray) ? implode(',', $procedureSurfaceArray) : null;

                    $combinationKey = $procedureId . '-' . $toothNumber;
                    if (!in_array($combinationKey, $uniqueCombinations)) {
                        $uniqueCombinations[] = $combinationKey;

                        $medicalRecord->procedures()->attach($procedureId, [
                            'tooth_number' => $toothNumber,
                            'notes' => $procedureNotes,
                            'surface' => $procedureSurface,
                        ]);
                    }
                }
            } else {
                // Jika prosedur tidak membutuhkan gigi, cukup simpan prosedurnya dengan notes (jika ada)
                $procedureNotes = $validatedData['procedure_notes'][$procedureId] ?? null;

                if (!in_array($procedureId, $uniqueCombinations)) {
                    $uniqueCombinations[] = $procedureId;

                    $medicalRecord->procedures()->attach($procedureId, [
                        'tooth_number' => null, // Tidak perlu gigi
                        'notes' => is_array($procedureNotes) ? implode(', ', $procedureNotes) : $procedureNotes,
                    ]);
                }
            }
        }


        $validatedData['updated_by'] = $updated_by;
        $medicalRecord->update($validatedData);

        // Hapus data lama dan masukkan data baru secara langsung
        // $medicalRecord->procedures()->detach();
        // foreach ($syncData as $procedureId => $entries) {
        //     foreach ($entries as $entry) {
        //         $medicalRecord->procedures()->attach($procedureId, $entry);
        //     }
        // }

        return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
            ->with('success', 'Medical record updated successfully.');
    }


    // public function destroy($patientId, $recordId)
    // {
    //     $medicalRecord = MedicalRecord::findOrFail($recordId);
    //     $medicalRecord->delete();

    //     return redirect()->route('dashboard.medical_records.index', ['patientId' => $patientId])
    //         ->with('success', 'Medical record deleted successfully.');
    // }

    public function destroyReservation($id)
    {
        // harus melakukan pengecekan, tidak bisa asal delete rekam medis
        $reservation = MedicalRecord::findOrFail($id);

        // Pengecekan apakah rekam medis masih kosong
        $hasTeethCondition = !empty($reservation->teeth_condition);
        $hasSubjective = !empty($reservation->subjective);
        $hasObjective = !empty($reservation->objective);
        $hasAssessment = !empty($reservation->assessment);
        $hasPlan = !empty($reservation->plan);
        $hasProcedures = $reservation->procedures()->exists();

        if ($hasTeethCondition || $hasSubjective || $hasObjective || $hasAssessment || $hasPlan || $hasProcedures) {
            return redirect()->route('dashboard.reservations.index')->with('error', 'Cannot delete reservation with existing medical record data.');
        }

        $reservation->delete();
        return redirect()->route('dashboard.reservations.index')->with('success', 'Reservation deleted successfully!');
    }

    public function selectForTransaction()
    {
        // Ambil semua rekam medis yang belum memiliki transaksi
        $medicalRecords = MedicalRecord::doesntHave('transaction')->get();

        return view('dashboard.medical_records.selectForTransaction', [
            'title' => 'Select Medical Record',
            'medicalRecords' => $medicalRecords,
        ]);
    }
}
