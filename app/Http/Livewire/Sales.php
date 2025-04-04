<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use Illuminate\Support\Facades\Cache;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;


class Sales extends Component
{

    use WithPagination;

    public $medicineSearch = '';

    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $cart = [];
    public $totalAmount = 0;
    public $saleCompleted = false;
    public $saleId;
    public $latestSales = [];

    public $selectedSale;
    public $selectedSaleItems = [];

    // Sale Return Properties
    public $returnSaleId;
    public $returnItems = [];
    public $saleReturns = [];


    protected $rules = [
        'selectedSaleItems.*.quantity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->fetchLatestSales();
    }

    // ðŸŸ¢ Load Existing Sale for Editing
    public function loadSale($saleId)
    {
        $this->selectedSale = Sale::find($saleId);
        $this->selectedSaleItems = SaleItem::where('sale_id', $saleId)->with('medicine')->get();
        $this->dispatchBrowserEvent('openEditSaleModal');
    }

    // ðŸŸ¢ Load Sale for Return
    public function loadReturnSale($saleId)
    {
        $this->returnSaleId = $saleId;
        $this->returnItems = SaleItem::where('sale_id', $saleId)->with('medicine')->get()->toArray();
        $this->dispatchBrowserEvent('openReturnSaleModal'); // Open modal in frontend
    }


    public function processSaleReturn()
    {
        $sale = Sale::find($this->returnSaleId); // Get the sale
        $totalAdjustment = 0; // Track total bill adjustment

        foreach ($this->returnItems as $index => $item) {
            $saleItem = SaleItem::where('sale_id', $sale->id)
                ->where('medicine_id', $item['medicine']['id'])
                ->first();

            if ($saleItem) {
                $medicine = Medicine::find($item['medicine']['id']); // Find medicine in stock
                $newQuantity = $item['quantity']; // Updated quantity from input
                $oldQuantity = $saleItem->quantity; // Original sold quantity

                if ($newQuantity < $oldQuantity) {
                    // **Return detected (Decrease in quantity)**
                    $returnedQuantity = $oldQuantity - $newQuantity;
                    $subtotal = $returnedQuantity * $saleItem->sale_price; // Calculate return amount

                    // **Update stock (Add back returned items)**
                    $medicine->total_units += $returnedQuantity;
                    $medicine->save();

                    // **Save sale return record**
                    SaleReturn::create([
                        'sale_id' => $sale->id,
                        'sale_item_id' => $saleItem->id,
                        'medicine_id' => $medicine->id,
                        'returned_quantity' => $returnedQuantity,
                        'return_amount' => $subtotal,
                    ]);

                    // **Adjust total sale amount**
                    $totalAdjustment -= $subtotal; // Subtract return amount
                } elseif ($newQuantity > $oldQuantity) {
                    // **Additional purchase detected (Increase in quantity)**
                    $addedQuantity = $newQuantity - $oldQuantity;
                    $additionalCost = $addedQuantity * $saleItem->sale_price; // Calculate extra cost

                    // **Check if stock is available**
                    if ($medicine->total_units >= $addedQuantity) {
                        // **Deduct stock**
                        $medicine->total_units -= $addedQuantity;
                        $medicine->save();

                        // **Adjust total sale amount**
                        $totalAdjustment += $additionalCost; // Add additional cost
                    } else {
                        session()->flash('error', "Not enough stock available for {$medicine->name}.");
                        return;
                    }
                }

                // **Update sale item quantity & subtotal**
                $saleItem->quantity = $newQuantity;
                $saleItem->subtotal = $newQuantity * $saleItem->sale_price; // Update subtotal
                $saleItem->save();
            }
        }

        // **Update sale total amount**
        $sale->total_amount += $totalAdjustment; // Adjust total
        $sale->save();

        // **Reset return items**
        $this->returnItems = [];
        $this->returnSaleId = null;

        // **Close modal and show success message**
        $this->dispatchBrowserEvent('closeReturnSaleModal');
        session()->flash('success', 'Sale update processed successfully!');
    }



    // ðŸŸ¢ Remove Sale Item
    public function removeSaleItem($itemId)
    {
        $saleItem = SaleItem::find($itemId);

        if (!$saleItem) {
            session()->flash('error', 'Item not found.');
            return;
        }

        if ($saleItem->quantity > 1) {
            $saleItem->quantity -= 1;
            $saleItem->subtotal = $saleItem->quantity * $saleItem->medicine->sale_price_per_unit;
            $saleItem->save();
        } else {
            $saleItem->delete();
        }

        $this->loadSale($saleItem->sale_id);
        session()->flash('success', 'Item updated successfully.');
    }

    public function fetchLatestSales()
    {
        $this->latestSales = Sale::latest()->get();
    }

    public function addMedicine($medicineId)
    {
        $medicine = Medicine::find($medicineId);
        if ($medicine) {
            $defaultQuantity = 1;

            foreach ($this->cart as $index => $item) {
                if ($item['id'] == $medicine->id) {
                    if (($this->cart[$index]['quantity'] + 1) > $medicine->total_units) {
                        session()->flash('error', 'Not enough stock available!');
                        return;
                    }

                    $this->cart[$index]['quantity'] += 1;
                    $this->cart[$index]['subtotal'] = $this->cart[$index]['quantity'] * $medicine->sale_price_per_unit;
                    $this->updateTotal();
                    return;
                }
            }

            $this->cart[] = [
                'id' => $medicine->id,
                'size' => $medicine->size,
                'name' => $medicine->name,
                'sale_price_per_unit' => $medicine->sale_price_per_unit,
                'quantity' => $defaultQuantity,
                'subtotal' => $defaultQuantity * $medicine->sale_price_per_unit
            ];
            $this->updateTotal();
        }
    }

    public function updateQuantity($index, $quantity)
    {
        $medicine = Medicine::find($this->cart[$index]['id']);

        if (!$medicine) {
            session()->flash('error', 'Medicine not found!');
            return;
        }
        if ($quantity > $medicine->total_units) {
            session()->flash('error', 'Not enough stock available!');
            return;
        }

        if ($quantity < 1) {
            session()->flash('error', 'Quantity must be at least 1!');
            return;
        }

        $this->cart[$index]['quantity'] = $quantity;
        $this->cart[$index]['subtotal'] = $quantity * $this->cart[$index]['sale_price_per_unit'];
        $this->updateTotal();
    }

    public function removeItem($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->updateTotal();
    }

    public function updateTotal()
    {
        $this->totalAmount = array_sum(array_column($this->cart, 'subtotal'));
    }

    public function completeSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'No items in cart!');
            return;
        }

        foreach ($this->cart as $item) {
            $medicine = Medicine::find($item['id']);
            if (!$medicine || $item['quantity'] > $medicine->total_units) {
                session()->flash('error', 'Not enough stock for ' . $item['name'] . '!');
                return;
            }
        }

        $roundedTotal = ceil($this->totalAmount);

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'total_amount' => $roundedTotal,
        ]);

        foreach ($this->cart as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'medicine_id' => $item['id'],
                'quantity' => $item['quantity'],
                'sale_price' => $item['sale_price_per_unit'],
                'subtotal' => $item['subtotal'],
            ]);

            // Reduce stock
            $medicine = Medicine::find($item['id']);
            if ($medicine) {
                $medicine->total_units -= $item['quantity'];
                $medicine->save();
            }
        }

        $this->cart = [];
        $this->totalAmount = 0;
        $this->saleCompleted = true;
        $this->saleId = $sale->id;

        $this->fetchLatestSales();
        session()->flash('success', 'Sale completed successfully!');
    }

    public function viewSaleDetails($saleId)
    {
        $this->selectedSale = Sale::find($saleId);
        $this->selectedSaleItems = SaleItem::where('sale_id', $saleId)->with('medicine')->get();
        $this->saleReturns = SaleReturn::where('sale_id', $saleId)->with('medicine')->get();

        $this->dispatchBrowserEvent('openViewSaleModal'); // Trigger modal
    }

    public function printInvoice($saleId)
    {
        return redirect()->route('invoice.print', ['saleId' => $saleId]);
    }

    public function updatingMedicineSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        // Try to get medicines from cache
        $medicinesCache = Cache::get('all_medicines');

        // If cache is empty, fetch from database and store in cache
        if (!$medicinesCache) {
            $medicinesCache = Medicine::select(['id', 'name', 'size', 'box_quantity', 'units_per_box', 'price', 'price_per_unit', 'sale_price', 'sale_price_per_unit'])->get();
            Cache::put('all_medicines', $medicinesCache, now()->addHours(24)); // Cache for 24 hours
        }

        // Filter medicines based on search input
        if ($this->medicineSearch) {
            $filteredMedicines = $medicinesCache->filter(function ($medicine) {
                return stripos($medicine->name, $this->medicineSearch) !== false;
            });
        } else {
            $filteredMedicines = $medicinesCache;
        }

        // Paginate the filtered medicines manually
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Adjust the number of items per page
        $currentPageMedicines = $filteredMedicines->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // Create the paginator
        $medicines = new LengthAwarePaginator(
            $currentPageMedicines,
            $filteredMedicines->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.sales', [
            'medicines' => $medicines,
        ]);
    }
}
