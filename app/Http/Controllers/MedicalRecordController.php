<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\StockCard;

use App\Models\Transaction;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use App\Models\JournalDetail;
use App\Models\MedicalRecord;
use App\Models\ChartOfAccount;
use App\Models\DentalMaterial;
use PHPUnit\Framework\Attributes\Medium;

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

    public function sendWhatsApp($id)
    {
        // Ambil data reservasi berdasarkan ID
        $reservation = MedicalRecord::with('patient')->findOrFail($id);

        // Nomor telepon pasien
        $phoneNumber = $reservation->patient->nomor_telepon;

        // Pesan template
        $message = "Halo {$reservation->patient->name}, untuk konfirmasi kehadiran di {$reservation->tanggal_reservasi} dan {$reservation->jam_mulai} ya. Terima kasih!";

        // Redirect ke wa.me dengan pesan template
        return redirect("https://wa.me/62{$phoneNumber}?text=" . urlencode($message));
    }

    public function waConfirmation($id)
    {
        // dd('oi');
        $reservation = MedicalRecord::findOrFail($id);
        $reservation->status_konfirmasi = 'Sudah Dikonfirmasi';
        $reservation->save();
        return redirect()->back()->with('success', 'Reservation confirmed successfully!');
    }
    
    public function index($patientId)
    {
        // Mengambil rekam medis langsung berdasarkan patient_id
        $medicalRecords = MedicalRecord::with(['procedures'])
            ->where('patient_id', $patientId)
            ->latest()
            ->get();

        $patientName = Patient::findOrFail($patientId)->name;
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
        // Ambil rekam medis berdasarkan ID
        $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

        $procedures = $medicalRecord->procedures;

        // Ambil daftar bahan dari prosedur yang terkait
        $dentalMaterialIds = $procedures->flatMap->dentalMaterials->pluck('id')->unique();

        // Ambil stok terbaru dari StockCard berdasarkan dental_material_id
        $stockCards = StockCard::select('dental_material_id', 'remaining_stock', 'average_price')
            ->whereIn('dental_material_id', $dentalMaterialIds)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('dental_material_id');

        // Menyimpan bahan yang dibutuhkan untuk setiap prosedur
        $materials = [];

        foreach ($procedures as $procedure) {
            foreach ($procedure->dentalMaterials as $material) {
                if (!isset($materials[$material->id])) {
                    // Cari stok terbaru untuk bahan ini
                    $stock = $stockCards->firstWhere('dental_material_id', $material->id);

                    // Menambahkan bahan hanya sekali, jika belum ada dalam array $materials
                    $materials[$material->id] = [
                        'name' => $material->name,
                        'stock_quantity' => $stock ? $stock->remaining_stock : 0, // Pakai stok terbaru
                        'average_price' => $stock ? $stock->average_price : 0, // Pakai harga rata-rata terbaru
                        'quantity' => $material->pivot->quantity, // Jumlah bahan yang diperlukan
                        'procedure_id' => $procedure->id // Hubungkan ke prosedur
                    ];
                } else {
                    // Jika bahan sudah ada, tambah jumlah kuantitas untuk prosedur yang sama
                    $materials[$material->id]['quantity'] += $material->pivot->quantity;
                }
            }
        }

        return view('dashboard.medical_records.selectMaterials', [
            'medicalRecordId' => $medicalRecordId,
            'procedures' => $procedures,
            'materials' => $materials,
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

    public function saveMaterials(Request $request, $medicalRecordId)
    {
        // dd('a');
        $medicalRecord = MedicalRecord::findOrFail($medicalRecordId);

        // Validasi input bahan dan kuantitas
        $validatedData = $request->validate([
            'quantities' => 'required|array',
        ]);

        $totalHPP = 0;

        // dd($validatedData['quantities']);

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
        // dd('saved');
        // dd($totalHPP);

        $transactionId = Transaction::where('medical_record_id', $medicalRecord->id)->value('id');

        // dd($transactionId);

        if ($totalHPP > 0) {
            // 1. Buat Journal Entry
            $journalEntry = new JournalEntry();
            $journalEntry->transaction_id = $transactionId; // Gunakan transaction_id dinamis
            $journalEntry->entry_date = now();
            $journalEntry->description = 'HPP untuk Prosedur pada Medical Record ' . $medicalRecord->id;
            $journalEntry->save();

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
        $validatedData = $request->validate([
            'teeth_condition' => 'required|string',
            'procedure_id' => 'required|array',
            'procedure_id.*' => 'exists:procedures,id',
            'teeth_condition' => 'required|string',
            'tooth_numbers' => 'nullable|array',
            'procedure_notes' => 'nullable|array',
        ]);

        // Ambil rekam medis berdasarkan ID
        $medicalRecord = MedicalRecord::findOrFail($recordId);

        // Update kondisi gigi pada rekam medis
        $medicalRecord->update(['teeth_condition' => $validatedData['teeth_condition']]);

        // Ambil daftar prosedur yang memerlukan nomor gigi
        $proceduresRequiringTeeth = Procedure::where('requires_tooth', 1)->pluck('id')->toArray();
        $uniqueCombinations = [];

        foreach ($validatedData['procedure_id'] as $procedureId) {
            if (in_array($procedureId, $proceduresRequiringTeeth)) {
                // Jika prosedur memerlukan nomor gigi, pastikan ada data
                if (!isset($validatedData['tooth_numbers'][$procedureId]) || !is_array($validatedData['tooth_numbers'][$procedureId])) {
                    return redirect()->back()->with('error', "Tooth number is required for procedure ID: $procedureId");
                }

                foreach ($validatedData['tooth_numbers'][$procedureId] as $toothNumber) {
                    $procedureNotes = $validatedData['procedure_notes'][$procedureId][$toothNumber] ?? null;

                    $combinationKey = $procedureId . '-' . $toothNumber;
                    if (!in_array($combinationKey, $uniqueCombinations)) {
                        $uniqueCombinations[] = $combinationKey;

                        $medicalRecord->procedures()->attach($procedureId, [
                            'tooth_number' => $toothNumber,
                            'notes' => $procedureNotes,
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
