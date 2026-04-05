# Al Falah Travels - Ticket & Invoice Generator

A complete PHP web application for travel agencies to generate professional booking tickets and invoices.

## Features

- **Booking Form**: Multi-passenger support, ENR/PNR reference, airline/airport dropdowns, baggage selection
- **Ticket Preview**: Professional e-ticket design with agency branding, print/save as PDF
- **Invoice Generator**: 4 beautiful templates (Modern, Classic, Minimal, Elegant), auto-calculations, WhatsApp sharing
- **Database Manager**: Full CRUD for airlines and airports — add, edit, delete entries
- **Agency Settings**: Logo, stamp, signature upload; agency details; terms & conditions

## Installation

1. Upload all files to your web hosting (any PHP hosting with Apache/Nginx)
2. Make sure the `data/` directory is writable by PHP: `chmod -R 755 data/`
3. Make sure the `data/uploads/` directory is writable: `chmod -R 755 data/uploads/`
4. Open your website URL in a browser — done!

## Requirements

- PHP 7.4 or higher
- Apache with mod_rewrite (recommended) or Nginx
- No database needed — uses JSON files for storage

## Directory Structure

```
ticket-generator/
├── index.php              # Main app page
├── .htaccess              # Apache config & security
├── css/style.css          # Custom styles
├── js/app.js              # Client-side JavaScript
├── includes/
│   ├── functions.php      # PHP utility functions
│   ├── booking-form.php   # Booking tab
│   ├── invoice-form.php   # Invoice tab
│   ├── database-manager.php # Database tab
│   └── agency-settings.php  # Settings tab
├── api/
│   ├── settings.php       # Settings API
│   ├── airlines.php       # Airlines CRUD API
│   └── airports.php       # Airports CRUD API
├── print/
│   ├── ticket.php         # Printable ticket page
│   └── invoice.php        # Printable invoice page
└── data/
    ├── agency-settings.json  # Agency settings
    ├── airlines.json         # Airlines database (65+ airlines)
    ├── airports.json         # Airports database (100+ airports)
    └── uploads/              # Uploaded images (logo, stamp, signature)
```

## Usage

1. Go to **Settings** tab first — set up your agency name, address, logo, stamp, and signature
2. Use **Booking** tab to create tickets — fill details, click "Preview Ticket" to open printable version
3. Use **Invoice** tab to create invoices — choose a template, fill details, preview & print
4. Use **Database** tab to manage airlines and airports as needed

## License

Private — Al Falah Travels
