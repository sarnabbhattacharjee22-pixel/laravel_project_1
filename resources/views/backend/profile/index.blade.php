@extends('backend.layout')

@section('backend_content')
<style>
    .admin-profile .profile-cover {
        position: relative;
        display: flex;
        min-height: 180px;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid #d9dee3;
        background-color: #f5f5f9;
        background-position: center;
        background-size: cover;
    }

    .admin-profile .profile-cover-placeholder {
        color: #697a8d;
    }

    .admin-profile .profile-cover-upload {
        position: absolute;
        right: 1rem;
        bottom: 1rem;
    }

    .admin-profile .profile-cover-input {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        clip-path: inset(50%);
    }

    .admin-profile .profile-cover-input:focus + .profile-cover-upload {
        outline: 2px solid #696cff;
        outline-offset: 2px;
    }

    .admin-profile .profile-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .admin-profile .profile-form-full {
        grid-column: 1 / -1;
    }

    @media (max-width: 767.98px) {
        .admin-profile .profile-form-grid {
            grid-template-columns: 1fr;
        }

        .admin-profile .profile-form-full {
            grid-column: auto;
        }
    }
</style>

<div class="admin-profile py-4">
    <div class="mb-4">
        <h4 class="mb-1">Profile settings</h4>
        <p class="mb-0 text-muted">Manage your profile details and password.</p>
    </div>

    <div class="row g-4">
        <section class="col-12 col-xl-8" aria-labelledby="profile-details-heading">
            <div class="card">
                <form
                    id="admin-update-form"
                    action="{{ route('admin.profile.update.info') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div
                        class="profile-cover"
                        id="cover-preview"
                        @if ($user->image)
                            style="background-image: url('{{ asset('storage/'.$user->image) }}')"
                        @endif
                    >
                        <span class="profile-cover-placeholder @if ($user->image) d-none @endif" id="cover-text">
                            No cover photo selected
                        </span>
                        <input
                            class="profile-cover-input"
                            type="file"
                            id="cover-input"
                            name="cover_photo"
                            accept="image/jpeg,image/png,image/webp"
                        >
                        <label for="cover-input" class="profile-cover-upload btn btn-light shadow-sm">
                            Upload cover
                        </label>
                    </div>
                    @error('cover_photo')
                        <div class="px-3 pt-2 text-danger">{{ $message }}</div>
                    @enderror

                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="mb-1" id="profile-details-heading">Profile details</h5>
                            <p class="mb-0 text-muted">Update your account information and cover photo.</p>
                        </div>

                        <div class="profile-form-grid">
                            <div>
                                <label for="full-name" class="form-label">Name</label>
                                <input
                                    type="text"
                                    id="full-name"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}"
                                    placeholder="Full name"
                                    autocomplete="name"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text" aria-hidden="true">@</span>
                                    <input
                                        type="text"
                                        id="username"
                                        name="username"
                                        class="form-control @error('username') is-invalid @enderror"
                                        value="{{ old('username', $user->username) }}"
                                        placeholder="username"
                                        autocomplete="username"
                                        required
                                    >
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="profile-form-full">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea
                                    id="bio"
                                    name="bio"
                                    class="form-control @error('bio') is-invalid @enderror"
                                    rows="4"
                                    placeholder="Write a short bio about yourself..."
                                >{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary" id="reset-btn">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="col-12 col-xl-4" aria-labelledby="password-update-heading">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0" id="password-update-heading">Password update</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update.own') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="previous-password" class="form-label">Previous password</label>
                            <input
                                type="password"
                                id="previous-password"
                                name="previous_password"
                                class="form-control @error('previous_password') is-invalid @enderror"
                                placeholder="Enter previous password"
                                autocomplete="current-password"
                                required
                            >
                            @error('previous_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new-password" class="form-label">New password</label>
                            <input
                                type="password"
                                id="new-password"
                                name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror"
                                placeholder="Enter new password"
                                autocomplete="new-password"
                                required
                            >
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="confirm-password" class="form-label">Confirm new password</label>
                            <input
                                type="password"
                                id="confirm-password"
                                name="confirm_password"
                                class="form-control"
                                placeholder="Confirm new password"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update password</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('backend_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('admin-update-form');
            const coverInput = document.getElementById('cover-input');
            const coverPreview = document.getElementById('cover-preview');
            const coverText = document.getElementById('cover-text');

            if (!form || !coverInput || !coverPreview || !coverText) {
                return;
            }

            let previewUrl = null;

            coverInput.addEventListener('change', () => {
                const file = coverInput.files?.[0];

                if (!file) {
                    return;
                }

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }

                previewUrl = URL.createObjectURL(file);
                coverPreview.style.backgroundImage = `url("${previewUrl}")`;
                coverText.classList.add('d-none');
            });

            form.addEventListener('reset', () => {
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }

                coverPreview.style.backgroundImage = '';
                coverText.classList.remove('d-none');
            });
        });

        @if (session()->has('success'))
            Swal.fire({
                title: 'Success!',
                text: @json(session('success')),
                icon: 'success',
                confirmButtonText: 'OK'
            });
        @endif
    </script>
@endpush
