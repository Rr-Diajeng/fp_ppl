@extends('layouts.layoutV1')
@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Approval Asset</h1>
        <div class="row">
            <div class="cdiv-xl-3 col-md-6">
                <p>The following is a list of Approval Assets that are waiting to be followed up on</p>
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
                            <th>Submit By</th>
                            @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'PIC')
                                <th>Action</th>
                            @endif
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
                                    <select name="status" id="status{{ $approval->slug }}">
                                        <option value="Pending" {{ $approval->status == 'Pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="Approved" {{ $approval->status == 'Approved' ? 'selected' : '' }}>
                                            Approved</option>
                                        <option value="Rejected" {{ $approval->status == 'Rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                </td>
                                <td>{{ $approval->reason }}</td>
                                <td>{{ $approval->created_at }}</td>
                                <td>{{ \App\Models\User::find($approval->UpdatedBy)->name }}</td>
                                @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'PIC')
                                    <td class="py-1.5 px-6">
                                        <div class="d-flex justify-content-center">
                                            <form id="form{{ $approval->slug }}" method="POST"
                                                action="{{ route('approveAsset', $approval->slug) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status"
                                                    id="hiddenStatus{{ $approval->slug }}">

                                                <button type="button" class="btn btn-primary btn-sm mb-2 me-2"
                                                    onclick="submitStatusUpdate('{{ $approval->slug }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                        <path
                                                            d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                                                    </svg>Update</button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
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
    <script>
        function submitStatusUpdate(slug) {
            const selectedStatus = document.getElementById('status' + slug).value;
            document.getElementById('hiddenStatus' + slug).value = selectedStatus;
            document.getElementById('form' + slug).submit();
        }
    </script>
@endsection
