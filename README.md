# OAuth Client Tester

A simple PHP-based OAuth 2.0 testing tool that allows you to test multiple OAuth providers and display authentication results.

## Features

- Test multiple OAuth 2.0 providers from a single application
- Support for OpenID Connect (OIDC) providers
- Display access tokens, refresh tokens, and user info
- Easy provider configuration via INI file

## Requirements

- PHP 7.4 or higher
- Composer
- Web server (Apache, Nginx, or PHP built-in server)

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd oauthclient
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Configure OAuth providers:
   ```bash
   cp etc/site.ini.example etc/site.ini
   ```

4. Edit `etc/site.ini` and add your OAuth provider configurations (see Configuration section below).

## Configuration

Edit `etc/site.ini` to configure OAuth providers:

```ini
[sys]
is_devel=1

[general]
# Space-separated list of provider names
tests=oauth_myprovider oauth_anotherprovider

[oauth_myprovider]
clientid=your-client-id
clientsecret=your-client-secret
authorizeurl=https://auth.example.com/oauth/authorize/
tokenurl=https://auth.example.com/oauth/token/
userinfourl=https://auth.example.com/oauth/userinfo/
# Optional OIDC endpoints
openidconfigurl=https://auth.example.com/.well-known/openid-configuration
logouturl=https://auth.example.com/oauth/logout/
jwksurl=https://auth.example.com/oauth/jwks/
```

### Required Fields per Provider

- `clientid` - OAuth client ID from your provider
- `clientsecret` - OAuth client secret from your provider
- `authorizeurl` - Authorization endpoint URL
- `tokenurl` - Token endpoint URL
- `userinfourl` - User info endpoint URL

### Optional Fields

- `openidconfigurl` - OpenID Connect configuration URL
- `openidissueurl` - OpenID issuer URL
- `logouturl` - Logout endpoint URL
- `jwksurl` - JSON Web Key Set URL

## Usage

### Running the Application

**Option 1: PHP Built-in Server**
```bash
cd public_html
php -S localhost:8000
```

Then navigate to `http://localhost:8000` in your browser.

**Option 2: Apache/Nginx**

Set your document root to the `public_html/` directory.

**Option 3: Using auto_prepend_file**

Configure PHP to automatically include the bootstrap:
```ini
auto_prepend_file=/path/to/oauthclient/phplib/stdinc.php
```

### Testing OAuth Flow

1. Open the application in your browser
2. Select a provider from the dropdown menu
3. Click "Change test" to load the provider configuration
4. The application will redirect you to the provider's login page
5. After authentication, you'll see:
   - Access token
   - Refresh token
   - Token expiration time
   - User information from the provider

## Development

### Debug Mode

Debug mode is enabled when:
- `[sys][is_devel]=1` in `etc/site.ini`
- Request originates from `127.0.0.1`

When enabled, debug functions (`decho()`, `debuglog()`) will output information.

### Project Structure

```
oauthclient/
├── etc/
│   ├── site.ini.example    # Example configuration
│   └── site.ini            # Your configuration (not in git)
├── phplib/
│   ├── Config.php          # Configuration parser
│   ├── stdinc.php          # Application bootstrap
│   ├── ui.php              # UI helper functions
│   └── utils.php           # Utility functions
├── public_html/
│   ├── index.php           # Main application
│   ├── css/                # Bootstrap 5 styles
│   └── js/                 # Bootstrap 5 scripts
├── vendor/                 # Composer dependencies
├── composer.json           # Composer configuration
└── README.md               # This file
```

### IDE Setup

**PHPStorm/IntelliJ IDEA:**
- Create a "PHP Web Server" run configuration
- Set document root to `public_html/`

## License

See LICENSE file for details.
