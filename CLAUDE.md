# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP-based OAuth 2.0 testing tool that allows testing multiple OAuth providers. It displays authentication request results and supports testing various OAuth/OIDC configurations.

## Architecture

### Core Components

**Configuration System (`phplib/Config.php`)**
- Custom INI file parser that reads `etc/site.ini`
- Structure: `$Config->get(section, variable, default)`
- Each OAuth provider is defined as a separate INI section prefixed with `oauth_`
- The `[general][tests]` key contains a space-separated list of provider names to enable

**Application Bootstrap (`phplib/stdinc.php`)**
- Entry point that initializes the entire application
- Sets up the global `$Config` object
- Includes all helper libraries (ui.php, utils.php)
- Loads Composer autoloader for OAuth2 client library
- Establishes session with name "oauthtester"
- Can be used via `auto_prepend_file` directive or direct include

**Main Application Flow (`public_html/index.php`)**
- Single-page application handling OAuth flow
- Uses League OAuth2 Client's GenericProvider
- Implements standard OAuth 2.0 authorization code flow with PKCE support
- Session-based state management (`$_SESSION['current_test']`, `$_SESSION['oauth2state']`, `$_SESSION['oauth2pkceCode']`)
- Displays access tokens, refresh tokens, and user info on successful authentication

### Helper Libraries

**UI Functions (`phplib/ui.php`)**
- `ui_header($title, $meta)` - Generates HTML header with Bootstrap 5 integration
- `ui_footer()` - Generates HTML footer
- `ui_error($err)`, `ui_success($msg)`, `ui_message($msg)` - Alert components
- Uses Bootstrap 5 for styling (CSS and JS in `public_html/css/` and `public_html/js/`)

**Utility Functions (`phplib/utils.php`)**
- `is_devel()` - Checks if running in dev mode (localhost + `[sys][is_devel]=1`)
- `decho($str)` - Debug output (only in dev mode)
- `debuglog($log)` - Debug logging to error_log (only in dev mode)
- Email validation with DNS MX record checks
- PHP size notation conversion utilities

## Development Setup

### Dependencies Installation

```bash
composer install
```

This installs `league/oauth2-client` (required for OAuth functionality).

### Configuration

1. Copy the example configuration:
   ```bash
   cp etc/site.ini.example etc/site.ini
   ```

2. Edit `etc/site.ini` to add OAuth providers:
   - Add provider names to `[general][tests]` (space-separated)
   - Create a section for each provider named `[oauth_providername]`
   - Required fields per provider:
     - `clientid` - OAuth client ID
     - `clientsecret` - OAuth client secret
     - `authorizeurl` - Authorization endpoint
     - `tokenurl` - Token endpoint
     - `userinfourl` - User info endpoint
     - Optional: `openidconfigurl`, `openidissueurl`, `logouturl`, `jwksurl`

### Web Server Setup

**Option 1: Using auto_prepend_file**
Configure PHP to automatically include the bootstrap file for all requests.

**Option 2: Direct include (current setup)**
The `public_html/index.php` includes `../phplib/stdinc.php` directly.

**Document root:** Point web server to `public_html/` directory.

### Testing OAuth Flows

1. Navigate to the application root URL
2. Select a provider from the dropdown (populated from `[general][tests]`)
3. Click "Change test" to load provider configuration
4. The application will redirect to the provider's authorization URL
5. After authentication, displays:
   - Access token
   - Refresh token
   - Token expiration
   - Resource owner details (from userinfo endpoint)

## Key Technical Details

### OAuth Implementation
- Uses `League\OAuth2\Client\Provider\GenericProvider`
- Redirect URI is automatically constructed as `http://{HTTP_HOST}`
- PKCE (Proof Key for Code Exchange) is enabled by default
- State parameter validation for CSRF protection
- Authorization code grant flow

### Session Management
- Session name: "oauthtester"
- Key session variables:
  - `current_test` - Currently selected provider name
  - `oauth2state` - CSRF protection state
  - `oauth2pkceCode` - PKCE code verifier

### Debug Mode
Debug features activate when:
1. `[sys][is_devel]=1` in configuration
2. Request originates from 127.0.0.1

When enabled: `decho()` outputs debug info, `debuglog()` writes to error_log.
