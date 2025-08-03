# Xero Dashboard Integration – Laravel + Inertia + React

This app is a simple yet robust demonstration of integrating with the [Xero API](https://developer.xero.com/) using **Laravel 12**, **Inertia.js**, and **React**. It authenticates users via OAuth 2.0 and presents selected accounting data in a responsive dashboard.

## Stack

* Laravel 12, Inertia.js (React), TailwindCSS via Laravel Starter Kit
* Xero PHP OAuth2 SDK (official)
* Vite (with HTTPS dev certs)
* Lando (for Dockerized local setup)
* SQLite (for simplicity in dev)

---

## Getting Started

### 1. Clone & Spin It Up with Lando

```bash
git clone https://github.com/eurechasherij/atas-challenge.git
cd atas-challenge/app
cp .env.example .env
lando start
lando artisan key:generate
```

### 2. Create Self-Signed SSL Certs for Vite (HMR via HTTPS)

Vite requires HTTPS to avoid CORS/OAuth issues with Xero in development. Run this once:

```bash
mkdir -p ./cert
openssl req -x509 -nodes -newkey rsa:2048 -keyout cert/localhost.key -out cert/localhost.crt -days 365 -subj "/CN=localhost"
```

Then edit `vite.config.js` to use the cert:

```js
server: {
  https: {
    key: fs.readFileSync('./cert/localhost.key'),
    cert: fs.readFileSync('./cert/localhost.crt'),
  },
  host: 'localhost',
}
```

### 3. Xero OAuth Setup

* Register at [https://developer.xero.com](https://developer.xero.com)
* Create a new "Web App"
* Set Redirect URI to `https://localhost/oauth/xero/callback`
* Grab your `CLIENT_ID` and `CLIENT_SECRET`

Update these in `.env`:

```
XERO_CLIENT_ID=...
XERO_CLIENT_SECRET=...
XERO_REDIRECT_URI=https://localhost/oauth/xero/callback
XERO_SCOPES
```

---

## Design Decisions

### Why Use the Official SDK?

Instead of `webfox/laravel-xero-oauth2`, I chose to use `xeroapi/xero-php-oauth2` directly because:

* It's officially supported by Xero, while the Laravel wrapper is unofficial and slightly outdated.
* Offers full OpenAPI spec coverage with predictable response models.
* Gives me full control over error handling, pagination, and edge cases without being boxed into a Laravel-specific abstraction.

### Why Modular Services?

Each data type (Organisation, Invoices, Contacts, Accounts) has its own Service class. This:

* Keeps `DataSyncService` lean and single-responsibility.
* Makes testing and mocking individual logic easier.
* Preps the codebase for later expansion (e.g., webhook syncs, cron jobs).

---

## Workarounds & Trade-Offs

### Bank Balance Retrieval

Xero's v10 SDK does not expose a direct method to fetch bank account balances (no `getBankAccountBalances` available).

**Workaround Used:**

* Fetched the `BankSummary` report via `getReportBankSummary()`
* Parsed the report rows manually to extract closing balance per bank account

**Pros:**

* Works reliably for demo purposes
* Avoids hardcoded logic or assumptions

**Cons:**

* Requires more parsing and assumption of report structure
* Not guaranteed to be real-time (summary report is pre-aggregated)

### Trade-offs Due to Time

* Didn't implement token refresh flow (access token is assumed valid)
* Used SQLite for ease, but a real setup would use Postgres or MySQL
* Styling is clean but minimal (functional, not pixel-perfect)

---

## Dashboard Features

* Organisation Info (Name, Country)
* 5 Most Recent Invoices
* 5 Most Recent Contacts
* Bank Accounts with Closing Balances

---

## Testing

```bash
lando artisan test
```

Includes:

* Unit tests for service fallbacks (no token, etc.)
* Mocked service construction

---

## Lando Commands

```bash
lando artisan migrate
lando artisan test
lando composer install
lando npm install
lando npm run dev
lando npm run build
```

## License

MIT. Built for the purpose of this challenge. Feel free to reuse with attribution.
