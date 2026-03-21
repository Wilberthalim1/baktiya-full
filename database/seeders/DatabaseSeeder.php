<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        $users = [
            ['name'=>'Administrator','email'=>'admin@baktiya.com','password'=>Hash::make('password'),'role'=>'admin','is_active'=>true],
            ['name'=>'Management','email'=>'management@baktiya.com','password'=>Hash::make('password'),'role'=>'management','is_active'=>true],
            ['name'=>'Sales Team','email'=>'sales@baktiya.com','password'=>Hash::make('password'),'role'=>'sales','is_active'=>true],
            ['name'=>'Purchasing Team','email'=>'purchasing@baktiya.com','password'=>Hash::make('password'),'role'=>'purchasing','is_active'=>true],
            ['name'=>'Invoicing Team','email'=>'invoicing@baktiya.com','password'=>Hash::make('password'),'role'=>'invoicing','is_active'=>true],
            ['name'=>'Warehouse Team','email'=>'warehouse@baktiya.com','password'=>Hash::make('password'),'role'=>'warehouse','is_active'=>true],
            ['name'=>'Accounting Team','email'=>'accounting@baktiya.com','password'=>Hash::make('password'),'role'=>'accounting','is_active'=>true],
        ];
        foreach ($users as $u) User::firstOrCreate(['email' => $u['email']], $u);

        // Categories
        $categories = [
            ['code'=>'ELC','name'=>'Elektronik'],
            ['code'=>'MEC','name'=>'Mekanikal'],
            ['code'=>'CHM','name'=>'Kimia'],
            ['code'=>'INS','name'=>'Instrumen'],
        ];
        foreach ($categories as $cat) Category::firstOrCreate(['code' => $cat['code']], $cat);

        // Customers
        $customers = [
            ['code'=>'CUST0001','name'=>'PT. Vale Indonesia','company'=>'PT. Vale Indonesia','email'=>'info@vale.com','phone'=>'021-5551234','city'=>'Jakarta','status'=>'active','credit_limit'=>100000000],
            ['code'=>'CUST0002','name'=>'PT. Riung Mitra Lestari','company'=>'PT. Riung Mitra Lestari','email'=>'cs@riungmitra.com','phone'=>'022-5559876','city'=>'Bandung','status'=>'active','credit_limit'=>50000000],
        ];
        foreach ($customers as $c) Customer::firstOrCreate(['code' => $c['code']], $c);

        // Suppliers
        $suppliers = [
            ['code'=>'SUPP0001','name'=>'PT. Sedaya Mitra Sejahtera','company'=>'PT. Sedaya Mitra Sejahtera','email'=>'sales@sedayamitra.com','phone'=>'061-4571757','city'=>'Medan','payment_term'=>30,'status'=>'active'],
            ['code'=>'SUPP0002','name'=>'3M Indonesia','company'=>'3M Indonesia','email'=>'order@3m.com','phone'=>'021-7779876','city'=>'Jakarta','payment_term'=>14,'status'=>'active'],
        ];
        foreach ($suppliers as $s) Supplier::firstOrCreate(['code' => $s['code']], $s);

        // Products
        $ins = Category::where('code','INS')->first()->id;
        $chm = Category::where('code','CHM')->first()->id;

        $products = [
            ['code'=>'PRD00001','name'=>'Yokogawa EJA50E In-Line Mount Gauge Pressure Transmitter','category_id'=>$ins,'unit'=>'PCS','cost_price'=>7000000,'selling_price'=>8900000,'stock_quantity'=>0,'min_stock'=>2,'max_stock'=>20,'is_active'=>true],
            ['code'=>'PRD00002','name'=>'3M Half Face Mask Reusable Respirator HF-802SD','category_id'=>$chm,'unit'=>'PCS','cost_price'=>450000,'selling_price'=>600000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00003','name'=>'3M Filter Holder Type 1700','category_id'=>$chm,'unit'=>'PCS','cost_price'=>30000,'selling_price'=>45000,'stock_quantity'=>0,'min_stock'=>10,'max_stock'=>200,'is_active'=>true],
            ['code'=>'PRD00004','name'=>'3M OV / AG Cartridges Type 6003','category_id'=>$chm,'unit'=>'PCS','cost_price'=>50000,'selling_price'=>70000,'stock_quantity'=>0,'min_stock'=>10,'max_stock'=>200,'is_active'=>true],
            ['code'=>'PRD00005','name'=>'3M Half Facepiece Respirator Type 77502','category_id'=>$chm,'unit'=>'PCS','cost_price'=>550000,'selling_price'=>730000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00006','name'=>'3M Goggle Splash Chemical Type 334 AF','category_id'=>$chm,'unit'=>'PCS','cost_price'=>65000,'selling_price'=>90000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00007','name'=>'3M Multiple Gas / Vapor Cartridges Type 6006','category_id'=>$chm,'unit'=>'PCS','cost_price'=>450000,'selling_price'=>600000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00008','name'=>'3M Full Facepiece Reusable Respirator 6000 Series (SMALL: 6700)','category_id'=>$chm,'unit'=>'PCS','cost_price'=>680000,'selling_price'=>900000,'stock_quantity'=>0,'min_stock'=>3,'max_stock'=>50,'is_active'=>true],
            ['code'=>'PRD00009','name'=>'3M Full Facepiece Reusable Respirator 6000 Series (MEDIUM: 6800)','category_id'=>$chm,'unit'=>'PCS','cost_price'=>880000,'selling_price'=>1150000,'stock_quantity'=>0,'min_stock'=>3,'max_stock'=>50,'is_active'=>true],
            ['code'=>'PRD00010','name'=>'3M Half Facepiece Reusable Respirator 6000 Series (MEDIUM: 6200)','category_id'=>$chm,'unit'=>'PCS','cost_price'=>330000,'selling_price'=>450000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00011','name'=>'3M Half Facepiece Reusable Respirator 6000 Series (LARGE: 6300)','category_id'=>$chm,'unit'=>'PCS','cost_price'=>510000,'selling_price'=>685000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00012','name'=>'3M Alcohol-Free Respirator Cleaning Wipe Type 504','category_id'=>$chm,'unit'=>'PACK','cost_price'=>560000,'selling_price'=>745000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
            ['code'=>'PRD00013','name'=>'3M Acid Gas Cartridges Type 6002','category_id'=>$chm,'unit'=>'PCS','cost_price'=>220000,'selling_price'=>300000,'stock_quantity'=>0,'min_stock'=>5,'max_stock'=>100,'is_active'=>true],
        ];
        foreach ($products as $p) Product::firstOrCreate(['code' => $p['code']], $p);

        echo "Seeding selesai!\n";
    }
}
