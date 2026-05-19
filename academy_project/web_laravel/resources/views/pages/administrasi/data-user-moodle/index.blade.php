@extends('components.main')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Data User Moodle</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    
                    {{-- 🔴 Tampilkan error jika Moodle tidak terhubung --}}
                    @if (!$moodleConnected)
                        <div class="alert alert-warning mx-3">
                            {{ $errorMessage ?? 'Tidak dapat mengambil data dari Moodle.' }}
                        </div>
                    @else
                        <div class="table-responsive pb-2 px-3">
                            <table id="example" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Username</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Firstname</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Lastname</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $users = $data['users'] ?? []; @endphp
                                    @forelse ($users as $index => $user)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">{{ $user['username'] ?? '' }}</td>
                                            <td class="text-center">{{ $user['firstname'] ?? '' }}</td>
                                            <td class="text-center">{{ $user['lastname'] ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data user.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
@endsection