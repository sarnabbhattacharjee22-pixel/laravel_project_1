@extends('backend.layout')

@section('backend_content')

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile Settings</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    body {
      color: #111827;
      display: flex;
      justify-content: center;
      padding: 40px 20px;
    }

    .admin-form-container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      overflow: hidden;
    }

    .cover-wrapper {
      position: relative;
      height: 160px;
      border-bottom: 1px solid #d1d5db;
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .cover-placeholder {
      color: #6b7280;
      font-size: 0.9rem;
    }

    .cover-upload-btn {
      position: absolute;
      bottom: 12px;
      right: 12px;
      background: #ffffff;
      color: #111827;
      border: 1px solid #d1d5db;
      padding: 6px 12px;
      border-radius: 6px;
      font-size: 0.85rem;
      cursor: pointer;
    }

    .cover-upload-btn:hover {
      border-color: #111827;
    }

    #cover-input {
      display: none;
    }

    .form-body {
      padding: 24px;
    }

    .form-header {
      margin-bottom: 20px;
    }

    .form-header h2 {
      font-size: 1.25rem;
      font-weight: 600;
    }

    .form-header p {
      color: #6b7280;
      font-size: 0.875rem;
      margin-top: 4px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-group.full-width {
      grid-column: span 2;
    }

    label {
      font-size: 0.85rem;
      font-weight: 500;
      color: #374151;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    input {
      width: 100%;
      background: transparent;
      border: 1px solid #d1d5db;
      color: #111827;
      padding: 10px 12px;
      border-radius: 6px;
      font-size: 0.95rem;
      outline: none;
    }

    input:focus {
      border-color: #111827;
    }

    .username-prefix {
      position: absolute;
      left: 12px;
      color: #6b7280;
      font-size: 0.95rem;
      pointer-events: none;
    }

    input.has-prefix {
      padding-left: 28px;
    }

    .toggle-password {
      position: absolute;
      right: 10px;
      background: transparent;
      border: none;
      color: #6b7280;
      cursor: pointer;
      font-size: 0.8rem;
    }

    .toggle-password:hover {
      color: #111827;
    }

    .form-actions {
      margin-top: 24px;
      padding-top: 16px;
      border-top: 1px solid #d1d5db;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .btn {
      padding: 9px 18px;
      border-radius: 6px;
      font-size: 0.9rem;
      font-weight: 500;
      cursor: pointer;
      background: transparent;
      border: 1px solid #d1d5db;
      color: #111827;
    }

    .btn:hover {
      border-color: #111827;
    }

    .btn-primary {
      border-color: #111827;
      font-weight: 600;
    }

    @media (max-width: 600px) {
      .form-grid {
        grid-template-columns: 1fr;
      }

      .form-group.full-width {
        grid-column: span 1;
      }
    }
  </style>
</head>
<body>

  <div class="admin-form-container">
    <form id="admin-update-form" action="/update-admin" method="POST" enctype="multipart/form-data">
      
      <div class="cover-wrapper" id="cover-preview">
        <span class="cover-placeholder" id="cover-text">No cover photo selected</span>
        <label for="cover-input" class="cover-upload-btn">Upload Cover</label>
        <input type="file" id="cover-input" name="cover_photo" accept="image/*">
      </div>

      <div class="form-body">
        <div class="form-header">
          <h2>Admin Profile</h2>
          <p>Update your account details and cover photo.</p>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label for="full-name">Name</label>
            <input type="text" id="full-name" name="name" placeholder="Full name" required>
          </div>

          <div class="form-group">
            <label for="username">Username</label>
            <div class="input-wrapper">
              <span class="username-prefix">@</span>
              <input type="text" id="username" name="username" class="has-prefix" placeholder="username" required>
            </div>
          </div>

          <div class="form-group full-width">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="admin@example.com" required>
          </div>

          <div class="form-group full-width">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <input type="password" id="password" name="password" placeholder="New password">
              <button type="button" class="toggle-password" id="toggle-pwd">Show</button>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="reset" class="btn" id="reset-btn">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>

    </form>
  </div>

  <script>
    const coverInput = document.getElementById('cover-input');
    const coverPreview = document.getElementById('cover-preview');
    const coverText = document.getElementById('cover-text');
    const resetBtn = document.getElementById('reset-btn');
    const passwordInput = document.getElementById('password');
    const togglePwdBtn = document.getElementById('toggle-pwd');

    coverInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
          coverPreview.style.backgroundImage = `url('${event.target.result}')`;
          coverText.style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    });

    resetBtn.addEventListener('click', () => {
      coverPreview.style.backgroundImage = '';
      coverText.style.display = 'block';
    });

    togglePwdBtn.addEventListener('click', () => {
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';
      togglePwdBtn.textContent = isPassword ? 'Hide' : 'Show';
    });
  </script>

</body>
</html>

@endsection