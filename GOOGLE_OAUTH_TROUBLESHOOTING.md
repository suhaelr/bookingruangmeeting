# Google OAuth Troubleshooting Guide

## Common Issues and Solutions

### 1. **Error 400: invalid_request**

This error typically occurs due to OAuth consent screen configuration issues.

#### Solution Steps:

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/
   - Select your project

2. **Configure OAuth Consent Screen**
   - Navigate to: APIs & Services → OAuth consent screen
   - **App name**: `Sistem Pemesanan Ruang Meeting`
   - **User support email**: `SuhaelR@gmail.com`
   - **Developer contact information**: `SuhaelR@gmail.com`
   - **App domain**: `www.pusdatinbgn.web.id`

3. **Add Required Scopes**
   - Click "Add or Remove Scopes"
   - Add these scopes:
     - `../auth/userinfo.email`
     - `../auth/userinfo.profile`
     - `openid`

4. **Configure Test Users (if in Testing mode)**
   - Add `SuhaelR@gmail.com` as a test user
   - Or publish the app for production use

### 2. **Error: App doesn't comply with OAuth 2.0 policy**

#### Solution:
1. **Check App Status**
   - Make sure the app is either in "Testing" or "Production" mode
   - If in Testing mode, add your email as a test user

2. **Verify App Information**
   - App name should be descriptive and match your business
   - Privacy policy and terms of service URLs should be valid
   - App logo should be appropriate

3. **Check Scopes**
   - Only request necessary scopes
   - Avoid requesting sensitive scopes unless absolutely necessary

### 3. **Redirect URI Mismatch**

#### Solution:
1. **Check Authorized Redirect URIs**
   - Go to: APIs & Services → Credentials
   - Click on your OAuth 2.0 Client ID
   - Add these URIs:
     - `https://www.pusdatinbgn.web.id/auth/google/callback`
     - `http://localhost:8000/auth/google/callback` (for testing)

2. **Check Authorized JavaScript Origins**
   - Add these origins:
     - `https://www.pusdatinbgn.web.id`
     - `http://localhost:8000` (for testing)

### 4. **Environment Variables Not Set**

#### Solution:
1. **Check .env file**
   ```env
   GOOGLE_CLIENT_ID=your_actual_client_id
   GOOGLE_CLIENT_SECRET=your_actual_client_secret
   GOOGLE_REDIRECT_URI=https://www.pusdatinbgn.web.id/auth/google/callback
   ```

2. **Clear config cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### 5. **Testing the Integration**

#### Local Testing:
1. **Start Laravel server**
   ```bash
   php artisan serve
   ```

2. **Test OAuth flow**
   - Go to `http://localhost:8000/login`
   - Click "Masuk dengan Google"
   - Complete the OAuth flow

#### Production Testing:
1. **Deploy to production**
2. **Test with production URL**
   - Go to `https://www.pusdatinbgn.web.id/login`
   - Test the OAuth flow

### 6. **Debugging Steps**

1. **Check Laravel logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Enable debug mode** (temporarily)
   ```env
   APP_DEBUG=true
   LOG_LEVEL=debug
   ```

3. **Check OAuth response**
   - Look for error parameters in the callback URL
   - Check the Laravel logs for detailed error messages

### 7. **Common OAuth Parameters**

The current implementation uses these parameters:
- `response_type=code`
- `access_type=online`
- `prompt=select_account`
- `scope=openid email profile`

These are the most compatible parameters for most OAuth implementations.

## Need Help?

If you're still experiencing issues:
1. Check the Google Cloud Console for any specific error messages
2. Review the Laravel logs for detailed error information
3. Verify all environment variables are correctly set
4. Ensure the OAuth consent screen is properly configured
