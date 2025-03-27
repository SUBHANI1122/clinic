<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class Sales extends Component
{
    public $search = '';
    public $medicines = [];
    public $cart = [];
    public $totalAmount = 0;
    public $saleCompleted = false;
    public $saleId;
    public $latestSales = [];

    public function mount()
    {
        $this->medicines = Medicine::all();
        $this->fetchLatestSales();
    }

    public function fetchLatestSales()
    {
        $this->latestSales = Sale::latest()->take(5)->get();
    }

    public function updatedSearch()
    {
        $this->medicines = Medicine::where('name', 'like', '%' . $this->search . '%')->get();
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

            if ($defaultQuantity <= $medicine->units_per_box) {
                $this->cart[] = [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'sale_price_per_unit' => $medicine->sale_price_per_unit,
                    'quantity' => $defaultQuantity,
                    'subtotal' => $defaultQuantity * $medicine->sale_price_per_unit
                ];
                $this->updateTotal();
            } else {
                session()->flash('error', 'Not enough stock available!');
            }
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
            if (!$medicine || $item['quantity'] > $medicine->units_per_box) {
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

    public function printInvoice($saleId)
    {
        return redirect()->route('invoice.print', ['saleId' => $saleId]);
    }

    public function render()
    {
        return view('livewire.sales');
    }
}
