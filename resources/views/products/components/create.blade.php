@extends('layouts.app')

@section('title', 'Add BOM Component')

@section('content')

<div class="page-header">
    <div>
        <h1>Add BOM Component</h1>

        <p>
            Add a material or product required to produce
            <strong>{{ $product->display_name }}</strong>.
        </p>
    </div>

    <a href="{{ route('products.show', $product) }}"
       class="btn btn-secondary">
        Back to Product
    </a>
</div>


<div class="card">

    <form
        action="{{ route('products.components.store', $product) }}"
        method="POST"
    >

        @csrf


        <div class="form-group">

            <label for="material_product_id">
                Material / Component
            </label>

            <select
                id="material_product_id"
                name="material_product_id"
                class="form-control"
                required
            >

                <option value="">
                    Select a component
                </option>

                @foreach($materials as $material)

                    <option
                        value="{{ $material->product_id }}"
                        {{ old('material_product_id') == $material->product_id ? 'selected' : '' }}
                    >
                        {{ $material->display_name }}
                        — {{ $material->stock_unit }}
                    </option>

                @endforeach

            </select>

            @error('material_product_id')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <div class="form-group">

            <label for="quantity_required">
                Quantity Required
            </label>

            <input
                type="number"
                id="quantity_required"
                name="quantity_required"
                class="form-control"
                value="{{ old('quantity_required') }}"
                min="0.01"
                step="0.01"
                placeholder="e.g. 8"
                required
            >

            @error('quantity_required')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>


        <button type="submit" class="btn btn-primary">
            Add Component
        </button>

        <a
            href="{{ route('products.show', $product) }}"
            class="btn btn-secondary"
        >
            Cancel
        </a>

    </form>

</div>

@endsection