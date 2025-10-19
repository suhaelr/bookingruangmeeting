# Google OAuth Integration

## Overview
This document describes the integration of Google OAuth authentication into the login page of the Laravel application.

## Implementation Details

### Frontend Integration
- **Location**: `resources/views/auth/login.blade.php`
- **Script**: Added Google OAuth API script in the head section
- **Client ID**: Configured via environment variable `GOOGLE_CLIENT_ID`
- **Button Styling**: Custom Google Sign-In button with 8px border radius

### Backend Integration
- **Location**: `app/Http/Controllers/AuthController.php`
- **Routes**: 
  - `GET /auth/google` - Redirects to Google OAuth
  - `GET /auth/google/callback` - Handles OAuth callback
- **Client Secret**: Configured via environment variable `GOOGLE_CLIENT_SECRET`
- **Redirect URI**: `https://www.pusdatinbgn.web.id/auth/google/callback`

### Database Changes
- **Migration**: `2025_10_10_185053_add_google_id_to_users_table.php`
- **New Column**: `google_id` (nullable, unique) in users table
- **Model Update**: Added `google_id` to User model fillable array

### Security Features
1. **CSRF Protection**: State parameter validation for OAuth flow
2. **User Creation**: Automatic user creation for new Google users
3. **Email Verification**: Google users are automatically verified
4. **Secure Token Exchange**: Server-side token exchange with Google
5. **Error Handling**: Comprehensive error handling and logging

### User Experience
- **Dual Authentication**: Users can choose between regular login or Google OAuth
- **Seamless Integration**: Google button styled to match the existing design
- **Consistent Styling**: Google button has 8px border radius
- **Visual Divider**: Clear separation between authentication methods

### OAuth Flow
1. User clicks "Masuk dengan Google" button
2. Redirected to Google OAuth consent screen
3. User authorizes the application
4. Google redirects back with authorization code
5. Server exchanges code for access token
6. Server retrieves user information from Google
7. User is created/updated in database
8. User is logged in and redirected to dashboard

### Configuration
The OAuth credentials are currently hardcoded in the application. For production deployment, consider moving these to environment variables:

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://www.pusdatinbgn.web.id/auth/google/callback
```

### Google Cloud Console Setup
1. **Authorized JavaScript origins**: `https://www.pusdatinbgn.web.id`
2. **Authorized redirect URIs**: `https://www.pusdatinbgn.web.id/auth/google/callback`
3. **OAuth consent screen**: Configured for the application
4. **Scopes**: `openid`, `email`, `profile`

### Styling Details
- **Google Button**: 8px border radius, white background, Google logo
- **Consistent Design**: Both elements match the overall login page design
- **Responsive**: Works on both desktop and mobile devices

### Testing
To test the integration:
1. Start the Laravel development server: `php artisan serve`
2. Navigate to the login page
3. Click "Masuk dengan Google" button
4. Complete Google OAuth flow
5. Verify user is created/logged in successfully
6. Test with existing users (should link Google account)

### Error Handling
- Invalid OAuth state parameter
- Missing authorization code
- Failed token exchange
- Failed user info retrieval
- Database errors during user creation/update
- All errors are logged for debugging

### Logging
All OAuth activities are logged including:
- Successful logins
- Failed attempts
- User creation
- Error details with stack traces
