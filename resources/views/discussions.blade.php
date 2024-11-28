@extends('layouts.app')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Forum Diskusi</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Daftar Diskusi -->
                        <div class="discussion-list flex-grow-1 overflow-auto mb-3">
                            @forelse ($discussions as $discussion)
                                <div class="discussion-item mb-3">
                                    <div class="d-flex justify-content-{{ $discussion->user_id === Auth::id() ? 'end' : 'start' }}">
                                        <div class="chat-bubble {{ $discussion->user_id === Auth::id() ? 'bg-primary text-white' : 'bg-light text-dark' }} p-3 rounded position-relative">
                                            <p class="mb-1"><strong>{{ $discussion->name }}</strong></p>
                                            <p class="mb-1">{{ $discussion->message }}</p>
                                            <small class="text-muted">{{ $discussion->created_at }}</small>

                                            <!-- Only show the delete button for admins, positioned inside the message bubble -->
                                            @if(Auth::user() && Auth::user()->role === 'admin')
                                                <button type="button" class="btn btn-link text-danger p-0 position-absolute top-0 end-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $discussion->id }}" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                </div>

                                <!-- Modal for Deletion Confirmation -->
                                <div class="modal fade" id="deleteModal{{ $discussion->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $discussion->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $discussion->id }}">Konfirmasi Penghapusan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Apakah Anda yakin ingin menghapus diskusi ini?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('discussions.destroy', $discussion->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">Belum ada diskusi.</p>
                            @endforelse
                        </div>

                        <!-- Form Tambah Pesan dan Button Kirim -->
                        <form action="{{ route('discussions.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="3" placeholder="Tulis pesan Anda..."></textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Kirim</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
