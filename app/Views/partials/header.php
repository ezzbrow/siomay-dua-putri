<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Siomay Dua Putri') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4C1D95;
            --primary-hover: #6D28D9;
            --secondary: #712edd;
            --secondary-light: #8B5CF6;
            --secondary-fixed: #ebddff;
            --background: #fef7ff;
            --surface: #ffffff;
            --surface-variant: #f3ebf6;
            --on-surface: #1d1a22;
            --on-surface-variant: #4a4452;
            --outline-variant: #e7e0eb;
            --accent: #e19760;

            --page-bg-gradient: linear-gradient(135deg, #F8F6FF 0%, #F2ECFF 100%);
            --card-shadow: 0 10px 30px rgba(76, 29, 149, 0.08);
            --card-shadow-hover: 0 20px 40px rgba(76, 29, 149, 0.12);

            --ok-bg: #E6F6EC;
            --ok-fg: #166534;
            --warn-bg: #FFF1D6;
            --warn-fg: #92400E;
            --err-bg: #FCE6E6;
            --err-fg: #991B1B;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-pill: 999px;

            --t-fast: 180ms ease-out;
            --t-base: 220ms ease-out;
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            font-family: "Be Vietnam Pro", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            background: var(--page-bg-gradient);
            color: var(--on-surface);
            line-height: 1.55;
            min-height: 100dvh;
            font-feature-settings: "cv11", "ss01";
        }
        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-hover); }
        button, input, select, textarea { font: inherit; color: inherit; }
        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
            vertical-align: middle;
            user-select: none;
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
