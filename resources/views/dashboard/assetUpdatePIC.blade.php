@extends('layouts.layoutV1')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Update Asset</h1>
        <div class="btn-back">
            <a href="{{ route('dashboard') }}" class="btn btn-primary mb-3 mt-3">Back to Dashboard</a>
        </div>
        <div class="d-flex justify-content-center">
            <div class="card mb-4 w-50">
                <div class="card-header">
                    <b>Update Data Asset</b>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('assetApprovalStore', $assetApproval->slug) }}" enctype="multipart/form-data">
                        @csrf
                        <div class= "mb-3">
                            <label for="assetName">Asset Name</label>
                            <input id="assetName" type="text" class="form-control" name="namaAsset" placeholder="Asset Name" value="{{ old('assetName', $assetApproval->namaAsset) }}" required>
                        </div>
                        <div class= "mb-3">
                            <label for="description">Description</label>
                            <textarea id="description" class="form-control" name="description" placeholder="Description about the Asset" rows="5" required>{{ old('description', $assetApproval->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="depreciation">Depreciation</label>
                            <input id="depreciation" type="number" class="form-control w-50" name="depreciation" placeholder="Depreciation the Asset" value="{{ old('depreciation',  $assetApproval->depreciation) }}" required>
                        </div>
                        <div class="mb-3">
                            <div class="">
                                <label for="assetImageUploader">Asset Image </label>
                                <input type="hidden" name="existingAssetImage" value="{{ $assetApproval->assetImage }}">
                            </div>
                            @if(empty($assetApproval->assetImage))
                                <small class="form-text text-muted">No image selected. The existing image will be used.</small>
                            @else
                                <small class="form-text text-muted">Current image: <img id="assetImagePreview" src="{{ asset('storage/' . $assetApproval->assetImage) }}" alt="Asset Image" style="width: 100px; height: auto;"></small>
                            @endif <br>
                            <small class="form-text text-muted">Wanna change your product photo? Click the button below</small>
                            <input id="assetImageUploader" type="file" class="form-control" name="assetImage" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="purchaseDate">Purchase Date</label>
                            <input id="purchaseDate" type="date" class="form-control" name="purchaseDate" value="{{ old('purchaseDate', $assetApproval->purchaseDate) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="price">Price</label>
                            <input id="price" type="number" class="form-control" name="price" placeholder="Price" value="{{ old('price', $assetApproval->price) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="price">Reason</label>
                            <input id="reason" type="text" class="form-control" name="reason" placeholder="Reason" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 w-100">Request Edit Data</button>
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
