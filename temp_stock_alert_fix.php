	public function stockAlertReport(Request $request)
	{
		$filters = json_decode($request->filters);

		$branchId = Auth::user()->branch_id;
		$productName = isset($filters->productName) ? $filters->productName : '';

		$query = Stock::where('branch_id', '=', $branchId)
			->whereRaw('qty <= min_stock');

		if (!empty($productName)) {
			$query->where('product_name', 'LIKE', '%' . $productName . '%');
		}

		$stocks = $query->orderBy('id', 'DESC')->get();

		// Load all suppliers at once
		$batchNos = $stocks->pluck('batch_no')->unique()->toArray();
		$suppliers = DB::table('pos_sub_receipts')
			->join('pos_receipts', 'pos_sub_receipts.pos_receipt_id', '=', 'pos_receipts.id')
			->join('profilers', 'pos_receipts.profile_id', '=', 'profilers.id')
			->whereIn('pos_sub_receipts.batch_no', $batchNos)
			->where('pos_receipts.type', 'PUR')
			->select('pos_sub_receipts.batch_no', 'profilers.account_title')
			->groupBy('pos_sub_receipts.batch_no', 'profilers.account_title')
			->get()
			->keyBy('batch_no');

		$record = [];
		$groupedBySupplier = [];
		
		foreach ($stocks as $stock) {
			$supplierName = isset($suppliers[$stock->batch_no]) ? $suppliers[$stock->batch_no]->account_title : 'Unknown Supplier';

			$item = [
				'productName' => $stock->product_name,
				'stripSize' => $stock->strip_size,
				'packSize' => $stock->pack_size,
				'batchNo' => $stock->batch_no,
				'qty' => $stock->qty,
				'expiryDate' => $stock->expiry_date,
				'minStock' => $stock->min_stock,
				'supplierName' => $supplierName
			];

			$record[] = $item;
			
			if (!isset($groupedBySupplier[$supplierName])) {
				$groupedBySupplier[$supplierName] = [];
			}
			$groupedBySupplier[$supplierName][] = $item;
		}

		return [
			'resultTitle' => !empty($productName) ? 'Search results for: ' . $productName : 'All stock alerts',
			'record' => $record,
			'groupedBySupplier' => $groupedBySupplier,
		];
	}
