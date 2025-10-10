# Environment Configuration

## Required Environment Variables

Add the following environment variables to your `.env` file:

### Google OAuth Configuration
```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=https://www.pusdatinbgn.web.id/auth/google/callback
```

### Cloudflare Turnstile Configuration
```env
TURNSTILE_SITE_KEY=your_turnstile_site_key_here
TURNSTILE_SECRET_KEY=your_turnstile_secret_key_here
```

## Production Setup

1. Copy your actual credentials to the `.env` file
2. Never commit the `.env` file to version control
3. Use environment-specific values for different deployments

## Security Notes

- Keep all OAuth credentials secure
- Use different credentials for development and production
- Regularly rotate your OAuth credentials
- Monitor OAuth usage in Google Cloud Console
