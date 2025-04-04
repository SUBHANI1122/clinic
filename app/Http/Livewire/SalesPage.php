<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleReturn;
use Illuminate\Support\Facades\Cache;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class SalesPage extends Component
{
    use WithPagination;

    public $medicineSearch = '';
    public $cart = [];
    public $totalAmount = 0;
    public $saleId;
    public $selectedSale;
    public $selectedSaleItems = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Fetch latest sales or perform any other setup here
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
        }

        // After sale, reset cart
        $this->cart = [];
        $this->updateTotal();
        session()->flash('success', 'Sale completed successfully!');
    }

    public function render()
    {
        // Fetch all medicines from cache or database
        $medicinesCache = Cache::get('all_medicines');

        // If cache is empty, fetch from the database and store in cache
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

        // Return the view with medicines and other variables
        return view('livewire.sales-page', [
            'medicines' => $medicines,
            'cart' => $this->cart,
            'totalAmount' => $this->totalAmount
        ]);
    }
}
