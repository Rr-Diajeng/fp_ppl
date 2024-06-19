@extends('layouts.layoutV1')
@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <div class="row">
            <div class="cdiv-xl-3 col-md-6">
                <p>The following is a list of assets in the database</p>
            </div>
        </div>
        @if (auth()->user()->role == 'Admin')
            <div class="btn-addAsset d-flex justify-content-between">
                <div>
                    <a href="{{ route('createAsset') }}" class="btn btn-primary mb-3">Add Asset</a>
                </div>
                <div>
                    @if (session('success'))
                        <div class="alert alert-success p-2">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger p-2">
                            <i class="fas fa-times-circle me-1"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Table Asset
            </div>
            <div class="card-body">
                <table id="dataTables">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Asset Name</th>
                            <th>Description</th>
                            <th>Depreciation</th>
                            <th>Product Photo</th>
                            <th>Price</th>
                            <th>Purchase Date</th>
                            <th>QRCode</th>
                            @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'PIC')
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($posts as $post)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $post->namaAsset }}</td>
                                <td>{{ Str::limit($post->description, 100) }}</td>
                                <td>{{ $post->depreciation }}</td>
                                <td><img src="{{ asset('storage/' . $post->assetImage) }}" alt="Product Photo"
                                        width="100" height="100"></td>
                                <td>Rp. {{ number_format($post->price) }}</td>
                                <td>{{ $post->purchaseDate }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $post->QRCode) }}" alt="QRCode" width="100" height="100">
                                    <br>
                                    <a href="{{ asset('storage/' . $post->QRCode) }}" download="{{ $post->QRCode }}" class="btn btn-primary mt-2">
                                        Download
                                    </a>
                                </td>
                                @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'PIC')
                                    <td class="py-1.5 px-6">
                                        <div class="d-flex justify-content-center">
                                            @if (auth()->user()->role == 'Admin')
                                                <a href="{{ route('editAsset', $post->slug) }}"
                                                    class="btn btn-primary btn-sm mb-2 me-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                        <path
                                                            d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <a href="{{ route('assetRemove', $post->slug) }}"
                                                    class="btn btn-sm mb-2 me-2 btn-outline-danger delete-btn"
                                                    data-confirm-delete="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                        <path
                                                            d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0">
                                                        </path>
                                                    </svg>
                                                    Delete
                                                </a>
                                            @endif
                                            @if (auth()->user()->role == 'PIC')
                                                <a href="{{ route('editAssetPIC', $post->slug) }}"
                                                    class="btn btn-primary btn-sm mb-2 me-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                        <path
                                                            d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5 a.5.5 0 0 1 .5.5 v.207zm-7.468 7.468 A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5 a.5.5 0 0 1-.175-.032l-.179.178 a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                            @endif
                                @endif
            </div>
            </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>No.</th>
                    <th>Asset Name</th>
                    <th>Description</th>
                    <th>Depreciation</th>
                    <th>Product Photo</th>
                    <th>Price</th>
                    <th>Purchase Date</th>
                    <th>QRCode</th>
                    @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'PIC')
                        <th>Action</th>
                    @endif
                </tr>
            </tfoot>
            </table>
        </div>
    </div>
    </div>
@endsection
