@extends('layouts.layoutV1')
@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">History Assets</h1>
        <div class="row">
            <div class="cdiv-xl-3 col-md-6">
                <p>The following is a list of History of Changes in Assets</p>
            </div>
        </div>
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
                            <th>Asset Link</th>
                            <th>Asset Name</th>
                            <th>Description</th>
                            <th>Depreciation</th>
                            <th>Product Photo</th>
                            <th>Price</th>
                            <th>Purchase Date</th>
                            <th>QRCode</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Update Submission Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($approvalAsset as $approval)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ \App\Models\Asset::where('id', $approval->assetID)->value('namaAsset') }}</td>
                                <td>{{ $approval->namaAsset }}</td>
                                <td>{{ Str::limit($approval->description, 100) }}</td>
                                <td>{{ $approval->depreciation }}</td>
                                <td><img src="{{ asset('storage/' . $approval->assetImage) }}" alt="Product Photo"
                                        width="100" height="100"></td>
                                <td>Rp. {{ number_format($approval->price) }}</td>
                                <td>{{ $approval->purchaseDate }}</td>
                                <td><img src="{{ asset('storage/' . $approval->QRCode) }}" alt="QRCode" width="100"
                                        height="100"></td>
                                <td>
                                    @if ($approval->status == 'Pending')
                                        <span class="badge bg-warning text-dark">{{ $approval->status }}</span>
                                    @elseif ($approval->status == 'Approved')
                                        <span class="badge bg-success">{{ $approval->status }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $approval->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $approval->reason }}</td>
                                <td>{{ $approval->created_at }}</td>
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
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
