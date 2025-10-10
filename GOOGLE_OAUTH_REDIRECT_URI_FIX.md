# Google OAuth Redirect URI Fix Guide

## Problem
Error 400: invalid_request with redirect_uri mismatch
```
Request details: redirect_uri=https%3A%2F%2Fwww.pusdatinbgn.web.id%2Fauth%2Fgoogle%2Fcallback flowName=GeneralOAuthFlow
```

## Root Cause
The redirect URI `https://www.pusdatinbgn.web.id/auth/google/callback` is not properly configured in Google Cloud Console.

## Solution Steps

### Step 1: Access Google Cloud Console
1. Go to: https://console.cloud.google.com/
2. Select your project
3. Navigate to: **APIs & Services** → **Credentials**

### Step 2: Update OAuth 2.0 Client ID
1. Click on your OAuth 2.0 Client ID
2. In the **"Authorized redirect URIs"** section, add these URIs:
   ```
   https://www.pusdatinbgn.web.id/auth/google/callback
   http://localhost:8000/auth/google/callback
   ```
3. Click **"Save"**

### Step 3: Verify OAuth Consent Screen
1. Go to: **APIs & Services** → **OAuth consent screen**
2. Ensure these settings:
   - **App name**: `Sistem Pemesanan Ruang Meeting`
   - **User support email**: `SuhaelR@gmail.com`
   - **Developer contact information**: `SuhaelR@gmail.com`

### Step 4: Check Scopes
1. In OAuth consent screen, click **"Add or Remove Scopes"**
2. Add these scopes:
   - `../auth/userinfo.email`
   - `../auth/userinfo.profile`
   - `openid`
3. Click **"Update"**

### Step 5: Test Users (if in Testing mode)
1. In OAuth consent screen, scroll to **"Test users"**
2. Click **"Add users"**
3. Add: `SuhaelR@gmail.com`
4. Click **"Save"**

### Step 6: Environment Variables
Make sure your `.env` file has:
```env
GOOGLE_CLIENT_ID=your_actual_client_id
GOOGLE_CLIENT_SECRET=your_actual_client_secret
GOOGLE_REDIRECT_URI=https://www.pusdatinbgn.web.id/auth/google/callback
```

### Step 7: Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
```

## Common Issues and Solutions

### Issue 1: "redirect_uri_mismatch"
**Solution**: Make sure the exact URI is added in Google Cloud Console
- Check for trailing slashes
- Check for http vs https
- Check for www vs non-www

### Issue 2: "invalid_client"
**Solution**: Verify client ID and secret are correct
- Check environment variables
- Clear Laravel cache
- Restart web server

### Issue 3: "access_denied"
**Solution**: Check OAuth consent screen
- Verify app name matches
- Add test users if in testing mode
- Check scopes are properly configured

### Issue 4: "invalid_request"
**Solution**: Check all parameters
- Verify redirect URI is exact match
- Check client ID is correct
- Ensure scopes are properly formatted

## Testing Steps

### Local Testing
1. Start Laravel server: `php artisan serve`
2. Go to: `http://localhost:8000/login`
3. Click "Masuk dengan Google"
4. Complete OAuth flow

### Production Testing
1. Deploy to production
2. Go to: `https://www.pusdatinbgn.web.id/login`
3. Click "Masuk dengan Google"
4. Complete OAuth flow

## Debugging

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

### Check OAuth Response
Look for error parameters in the callback URL:
- `error=access_denied`
- `error=invalid_request`
- `error=redirect_uri_mismatch`

### Verify Environment Variables
```bash
php artisan tinker
>>> env('GOOGLE_CLIENT_ID')
>>> env('GOOGLE_REDIRECT_URI')
```

## Expected Behavior After Fix

1. Click "Masuk dengan Google" on login page
2. Redirected to Google OAuth consent screen
3. App name shows: "Sistem Pemesanan Ruang Meeting"
4. User can authorize the app
5. Redirected back to your application
6. User is logged in successfully

## Still Having Issues?

If you're still experiencing problems:
1. Double-check all URIs match exactly
2. Verify client ID and secret are correct
3. Check that the app is not in "Testing" mode (or add your email as test user)
4. Ensure all required scopes are added
5. Clear all caches and restart services

The most common cause is the redirect URI not being exactly matched in Google Cloud Console.
