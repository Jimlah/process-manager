# SIGNAL - Elegant Process Management

SIGNAL is a modern process manager designed for clarity, reactivity, and control. It provides a beautiful interface to monitor and manage multiple development commands and services simultaneously.

![SIGNAL Dashboard](screenshots/dashboard.png)

## Core Features

### 📂 Project & Command Organization

Group your microservices, build scripts, and dev servers into logical projects. SIGNAL provides a clear, high-level overview of your entire development environment.

### 🖥️ High-Performance Terminal

Monitor process output in real-time. The built-in terminal features full ANSI escape code support, ensuring that colors, styles, and spacing look exactly as they do in your native command line.

![Process Runner](screenshots/process_runner.png)

### ⚡ Instant Control

Start, stop, and restart processes with immediate feedback. The interface is built for speed, allowing you to manage complex service dependencies with zero friction.

### 🎨 Advanced Theming

Personalize SIGNAL to match your workflow. Import VS Code themes or switch between curated Light, Dark, and System appearance modes instantly.

![Theming](screenshots/light_theme.png)

### 🔔 Desktop Native Experience

Built as a native application, SIGNAL integrates deeply with your desktop environment, providing notifications for long-running processes and a focused workspace outside of the browser.

## 📄 License

The SIGNAL process manager is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🏗️ Building & Publishing

SIGNAL is packaged as a desktop application using [NativePHP](https://nativephp.com/).

### Building

To compile the application into a production-ready state, run the build command for your target platform:

```bash
php artisan native:build [mac|win|linux]
```

This process automatically compiles frontend assets (via `npm run build`) before bundling the application.

#### Cross-Compilation

- **Mac**: Must be compiled on a Mac.
- **Windows**: Can be cross-compiled on Linux using `wine32`.

### Code Signing

Both macOS and Windows require your app to be signed before distribution.

#### macOS

Requires an Apple Developer Account. When running the build command, provide the following environment variables (or add them to your `.env`):

- `NATIVEPHP_APPLE_ID`
- `NATIVEPHP_APPLE_ID_PASS` (App-specific password)
- `NATIVEPHP_APPLE_TEAM_ID`

#### Windows

Azure Trusted Signing is recommended over traditional certificates. Provide the following in your `.env`:

- `AZURE_TENANT_ID`, `AZURE_CLIENT_ID`, `AZURE_CLIENT_SECRET`
- `NATIVEPHP_AZURE_PUBLISHER_NAME`
- `NATIVEPHP_AZURE_ENDPOINT`
- `NATIVEPHP_AZURE_CERTIFICATE_PROFILE_NAME`
- `NATIVEPHP_AZURE_CODE_SIGNING_ACCOUNT_NAME`

### Publishing to GitHub

NativePHP can automatically upload your build artifacts to a provider (like GitHub Releases) to enable automatic updates. This project is configured to publish to GitHub by default.

#### Requirements

1. Create a **Draft Release** on your GitHub repository.
2. Set the "Tag version" to `v` + the `version` configured in your `nativephp.php` config (or `.env` via `NATIVEPHP_APP_VERSION`), e.g., `v1.0.0`.
3. Provide the following environment variables (or add them to `.env`):
    - `GITHUB_REPO`: Your repository name
    - `GITHUB_OWNER`: Your repository owner/organization
    - `GITHUB_TOKEN`: A personal access token with `repo` scope

#### Running the Publish Command

Once the draft release is ready and credentials are set, run:

```bash
php artisan native:publish [mac|win|linux]
```

Your build artifacts will automatically be attached to the draft release.
