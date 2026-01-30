# Zoho CRM Form

Laravel + Inertia.js + Vue 3 application for creating Accounts and Deals in Zoho CRM via a two-step wizard.

[Video Demo](https://drive.google.com/file/d/1eA3Jfm5moEulssNAL4q6nJy3eCzJ0nMO/view?usp=sharing)

## Tech Stack

- **Backend:** Laravel 12, Inertia.js, Spatie Laravel Data
- **Frontend:** Vue 3 (Composition API), Inertia.js, Tailwind CSS 4
- **Database:** SQLite
- **Build:** Vite 7

## Requirements

- PHP 8.2+
- Node.js 18+
- Composer
- Zoho CRM account with API access

## Setup

```bash
git clone <repo-url> zoho_crm_form
cd zoho_crm_form
composer install
npm install
cp .env.example .env
php artisan key:generate
```

### Zoho API Credentials

Register an application in the [Zoho API Console](https://api-console.zoho.com/) with type **Server-based Applications**.

Set the redirect URI to `http://<your-domain>/zoho/callback`.

Add to `.env`:

```dotenv
ZOHO_CLIENT_ID=your_client_id
ZOHO_CLIENT_SECRET=your_client_secret
ZOHO_REDIRECT_URI=http://localhost:8000/zoho/callback
ZOHO_ACCOUNTS_URL=https://accounts.zoho.com
ZOHO_API_URL=https://www.zohoapis.com
```

> For EU data centers use `https://accounts.zoho.eu` and `https://www.zohoapis.eu`.

### Database

```bash
touch database/database.sqlite
php artisan migrate
```

### Run

```bash
composer dev
```

This starts the Laravel server, queue worker, log viewer and Vite dev server concurrently.

Or run them separately:

```bash
php artisan serve
npm run dev
```

Build for production:

```bash
npm run build
```


## Routes

| Method | URI              | Description                |
|--------|------------------|----------------------------|
| GET    | `/`              | CRM form (wizard)          |
| GET    | `/auth`          | Connect to Zoho screen     |
| GET    | `/zoho/auth`     | Start OAuth flow           |
| GET    | `/zoho/callback` | OAuth callback             |
| POST   | `/crm/account`   | Create Account (step 1)    |
| POST   | `/crm/deal`      | Create Deal (step 2)       |

## How It Works

1. User visits `/` — if no valid Zoho token exists for the session, redirected to `/auth`.
2. User clicks "Connect Zoho Account" — OAuth flow with Zoho, token stored with `session_id`.
3. **Step 1:** User fills Account fields (name, website, phone) and submits. Account is created in Zoho CRM.
4. **Step 2:** User fills Deal fields (name, stage) and submits. Deal is created and linked to the Account.

### Token Refresh

Access tokens are refreshed automatically — users don't need to re-authorize when a token expires:

- **Middleware-level refresh:** On every page load the Inertia middleware checks if the access token is expired. If a refresh token is available, it refreshes the access token transparently before rendering the page.
- **API-level retry:** If a Zoho API call returns `401 Unauthorized`, the service layer refreshes the token and retries the request once.
- Re-authorization is only required when the refresh token itself becomes invalid (e.g. revoked in Zoho API Console).

