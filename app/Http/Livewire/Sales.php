<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleReturn;

class Sales extends Component
{
    public $medicineSearch = ''; // Updated search variable
    public $medicines = [];
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
        $this->medicines = Medicine::all();
        $this->fetchLatestSales();
    }

    // 🟢 Search Medicine in Real-time
    public function updatedMedicineSearch()
    {
        $this->medicines = Medicine::where('name', 'like', '%' . $this->medicineSearch . '%')->get();
    }

    // 🟢 Load Existing Sale for Editing
    public function loadSale($saleId)
    {
        $this->selectedSale = Sale::find($saleId);
        $this->selectedSaleItems = SaleItem::where('sale_id', $saleId)->with('medicine')->get();
        $this->dispatchBrowserEvent('openEditSaleModal');
    }

    // 🟢 Load Sale for Return
    public function loadReturnSale($saleId)
    {
        $this->returnSaleId = $saleId;
        $this->returnItems = SaleItem::where('sale_id', $saleId)->with('medicine')->get()->toArray();
        $this->dispatchBrowserEvent('openReturnSaleModal'); 
    }

    public function viewSaleDetails($saleId)
    {
        $this->selectedSale = Sale::find($saleId);
        $this->selectedSaleItems = SaleItem::where('sale_id', $saleId)->with('medicine')->get();
        $this->saleReturns = SaleReturn::where('sale_id', $saleId)->with('medicine')->get();

        $this->dispatchBrowserEvent('openViewSaleModal'); 
    }

    public function processSaleReturn()
    {
        $sale = Sale::find($this->returnSaleId);
        $totalAdjustment = 0;

        foreach ($this->returnItems as $index => $item) {
            $saleItem = SaleItem::where('sale_id', $sale->id)
                ->where('medicine_id', $item['medicine']['id'])
                ->first();

            if ($saleItem) {
                $medicine = Medicine::find($item['medicine']['id']);
                $newQuantity = $item['quantity'];
                $oldQuantity = $saleItem->quantity;

                if ($newQuantity < $oldQuantity) {
                    $returnedQuantity = $oldQuantity - $newQuantity;
                    $subtotal = $returnedQuantity * $saleItem->sale_price;

                    $medicine->total_units += $returnedQuantity;
                    $medicine->save();

                    SaleReturn::create([
                        'sale_id' => $sale->id,
                        'sale_item_id' => $saleItem->id,
                        'medicine_id' => $medicine->id,
                        'returned_quantity' => $returnedQuantity,
                        'return_amount' => $subtotal,
                    ]);

                    $totalAdjustment -= $subtotal;
                } elseif ($newQuantity > $oldQuantity) {
                    $addedQuantity = $newQuantity - $oldQuantity;
                    $additionalCost = $addedQuantity * $saleItem->sale_price;

                    if ($medicine->total_units >= $addedQuantity) {
                        $medicine->total_units -= $addedQuantity;
                        $medicine->save();

                        $totalAdjustment += $additionalCost;
                    } else {
                        session()->flash('error', "Not enough stock available for {$medicine->name}.");
                        return;
                    }
                }

                $saleItem->quantity = $newQuantity;
                $saleItem->subtotal = $newQuantity * $saleItem->sale_price;
                $saleItem->save();
            }
        }

        $sale->total_amount += $totalAdjustment;
        $sale->save();

        $this->returnItems = [];
        $this->returnSaleId = null;

        $this->dispatchBrowserEvent('closeReturnSaleModal');
        session()->flash('success', 'Sale update processed successfully!');
    }

    // 🟢 Remove Sale Item
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

    public function render()
    {
        return view('livewire.sales');
    }
}
