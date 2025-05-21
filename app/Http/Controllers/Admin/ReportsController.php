<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VinylSec;
use App\Models\VinylMaster;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        // Dashboard geral com links para os diversos relatórios
        return view('admin.reports.index');
    }

    public function vinyl()
    {
        // Estatísticas gerais de estoque
        $totalDiscs = VinylSec::count();
        $availableDiscs = VinylSec::where('in_stock', true)->count();
        $unavailableDiscs = $totalDiscs - $availableDiscs;
        
        // Valores totais
        $totalBuyValue = VinylSec::sum('buy_price');
        $totalSellValue = VinylSec::sum('price');
        $potentialProfit = $totalSellValue - $totalBuyValue;
        
        // Dados para a lista de discos
        $discs = VinylSec::with(['vinylMaster', 'vinylMaster.artists', 'supplier', 'midiaStatus', 'coverStatus'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Dados agrupados por fornecedor com supplier_id não nulo
        $supplierStats = VinylSec::select(
                DB::raw('IFNULL(supplier_id, 0) as supplier_id'), 
                DB::raw('COUNT(*) as total_discs'),
                DB::raw('SUM(buy_price) as total_buy'),
                DB::raw('SUM(price) as total_sell'),
                DB::raw('SUM(case when in_stock = 1 then 1 else 0 end) as available')
            )
            ->groupBy(DB::raw('IFNULL(supplier_id, 0)'))
            ->get();
            
        // Enriquecendo dados com nomes dos fornecedores
        $supplierStats->map(function($item) {
            if ($item->supplier_id == 0) {
                $item->supplier_name = 'Origem Desconhecida';
            } else {
                $supplier = Supplier::find($item->supplier_id);
                $item->supplier_name = $supplier ? $supplier->name : 'Fornecedor Inválido';
            }
            return $item;
        });

        return view('admin.reports.vinyl', compact(
            'totalDiscs', 
            'availableDiscs', 
            'unavailableDiscs', 
            'totalBuyValue', 
            'totalSellValue', 
            'potentialProfit',
            'discs',
            'supplierStats'
        ));
    }
}
