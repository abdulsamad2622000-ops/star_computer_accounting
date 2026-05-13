@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
        @endif

        <!-- Edit Product -->
        <div class="form-card mb-4">
            <h5 class="fw-bold mb-4">
                <i class="bi bi-box-seam me-2"></i>Edit Product
            </h5>

           <!-- Stock Info — Editable -->
<div class="form-card mb-4">
    <h5 class="fw-bold mb-4">
        <i class="bi bi-box-seam me-2"></i>Edit Product
    </h5>

    <form action="{{ route('products.update', $product) }}"
          method="POST">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Received Qty</label>
                <input type="number" name="received_qty"
                       class="form-control"
                       value="{{ $product->received_qty }}"
                       min="0">
            </div>
            <div class="col-md-4">
                <label class="form-label text-danger fw-bold">
                    Sold Qty
                    <small class="text-muted">(Auto)</small>
                </label>
                <input type="number"
                       class="form-control"
                       value="{{ $product->sold_qty }}"
                       readonly
                       style="background:#fef2f2">
            </div>
            <div class="col-md-4">
                <label class="form-label text-success fw-bold">Remaining Qty</label>
                <input type="number" name="remaining_qty"
                       class="form-control"
                       value="{{ $product->remaining_qty }}"
                       min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name"
                       class="form-control" required
                       value="{{ $product->name }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">
                    Stock Code
                    <small class="text-muted">(Optional)</small>
                </label>
                <input type="text" name="stock_code"
                       class="form-control"
                       value="{{ $product->stock_code }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Purchase Price</label>
                <input type="number" name="purchase_price"
                       class="form-control"
                       value="{{ $product->purchase_price }}"
                       min="0" step="0.01">
            </div>
            <div class="col-md-6">
                <label class="form-label">Sale Price</label>
                <input type="number" name="sale_price"
                       class="form-control"
                       value="{{ $product->sale_price }}"
                       min="0" step="0.01">
            </div>
            <div class="col-md-6">
                <label class="form-label">Alert Qty</label>
                <input type="number" name="alert_qty"
                       class="form-control"
                       value="{{ $product->alert_qty }}"
                       min="1">
            </div>
            <div class="col-md-6">
                <label class="form-label">Vendor</label>
                <select name="vendor_id" class="form-select">
                    <option value="">Select...</option>
                    @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}"
                        {{ $product->vendor_id == $vendor->id ? 'selected' : '' }}>
                        {{ $vendor->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Update Product
            </button>
            <a href="{{ route('products.index') }}"
               class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

       

              


    </div>
</div>
@endsection