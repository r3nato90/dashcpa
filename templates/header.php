<?php
// O session_start() deve estar no topo de cada página ANTES de incluir este header.
?>
<!DOCTYPE html>
<html lang="pt-br" class="light"> <!-- Força o 'light mode' do seu CSS -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dash CPA</title>
    
    <!-- FontAwesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert para pop-ups -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.17/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Bootstrap (necessário para os dropdowns) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="/favicon/favicon.jpeg" type="image/jpeg">

    <style>
        /*
        * CSS FORNECIDO PELO USUÁRIO (TAILWIND/SHADCN VARS E UTILITIES)
        * Define as variáveis de cor para .light (padrão) e .dark
        */
        *,:before,:after{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }*,:before,:after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}:before,:after{--tw-content: ""}html,:host{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dl,dd,h1,h2,h3,h4,h5,h6,hr,figure,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}ol,ul,menu{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}button,[role=button]{cursor:pointer}:disabled{cursor:default}img,svg,video,canvas,audio,iframe,embed,object{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}
        :root{--background: 0 0% 100%;--foreground: 222.2 84% 4.9%;--card: 0 0% 100%;--card-foreground: 222.2 84% 4.9%;--popover: 0 0% 100%;--popover-foreground: 222.2 84% 4.9%;--primary: 222.2 47.4% 11.2%;--primary-foreground: 210 40% 98%;--secondary: 210 40% 96.1%;--secondary-foreground: 222.2 47.4% 11.2%;--muted: 210 40% 96.1%;--muted-foreground: 215.4 16.3% 46.9%;--accent: 210 40% 96.1%;--accent-foreground: 222.2 47.4% 11.2%;--destructive: 0 84.2% 60.2%;--destructive-foreground: 210 40% 98%;--border: 214.3 31.8% 91.4%;--input: 214.3 31.8% 91.4%;--ring: 222.2 84% 4.9%;--radius: .5rem;--sidebar-background: 0 0% 98%;--sidebar-foreground: 240 5.3% 26.1%;--sidebar-primary: 240 5.9% 10%;--sidebar-primary-foreground: 0 0% 98%;--sidebar-accent: 240 4.8% 95.9%;--sidebar-accent-foreground: 240 5.9% 10%;--sidebar-border: 220 13% 91%;--sidebar-ring: 217.2 91.2% 59.8%}
        .dark{--background: 222.2 84% 4.9%;--foreground: 210 40% 98%;--card: 222.2 84% 4.9%;--card-foreground: 210 40% 98%;--popover: 222.2 84% 4.9%;--popover-foreground: 210 40% 98%;--primary: 210 40% 98%;--primary-foreground: 222.2 47.4% 11.2%;--secondary: 217.2 32.6% 17.5%;--secondary-foreground: 210 40% 98%;--muted: 217.2 32.6% 17.5%;--muted-foreground: 215 20.2% 65.1%;--accent: 217.2 32.6% 17.5%;--accent-foreground: 210 40% 98%;--destructive: 0 62.8% 30.6%;--destructive-foreground: 210 40% 98%;--border: 217.2 32.6% 17.5%;--input: 217.2 32.6% 17.5%;--ring: 212.7 26.8% 83.9%;--sidebar-background: 240 5.9% 10%;--sidebar-foreground: 240 4.8% 95.9%;--sidebar-primary: 224.3 76.3% 48%;--sidebar-primary-foreground: 0 0% 100%;--sidebar-accent: 240 3.7% 15.9%;--sidebar-accent-foreground: 240 4.8% 95.9%;--sidebar-border: 240 3.7% 15.9%;--sidebar-ring: 217.2 91.2% 59.8%}
        *,:before,:after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::backdrop{--tw-border-spacing-x: 0;--tw-border-spacing-y: 0;--tw-translate-x: 0;--tw-translate-y: 0;--tw-rotate: 0;--tw-skew-x: 0;--tw-skew-y: 0;--tw-scale-x: 1;--tw-scale-y: 1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness: proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width: 0px;--tw-ring-offset-color: #fff;--tw-ring-color: rgb(59 130 246 / .5);--tw-ring-offset-shadow: 0 0 #0000;--tw-ring-shadow: 0 0 #0000;--tw-shadow: 0 0 #0000;--tw-shadow-colored: 0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }*,:before,:after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}:before,:after{--tw-content: ""}html,:host{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dl,dd,h1,h2,h3,h4,h5,h6,hr,figure,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}ol,ul,menu{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}button,[role=button]{cursor:pointer}:disabled{cursor:default}img,svg,video,canvas,audio,iframe,embed,object{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}*{border-color:hsl(var(--border))}body{background-color:hsl(var(--background));color:hsl(var(--foreground))}
        /* ... O restante do seu CSS (utilitários .container, .dark, .text-sm, etc.) ... */
        .container{width:100%;margin-right:auto;margin-left:auto;padding-right:2rem;padding-left:2rem}@media (min-width: 1400px){.container{max-width:1400px}}
        /* ... etc ... */


        /*
        * ===================================================================
        * ESTILOS DO NOVO LAYOUT (SIDEBAR + CONTEÚDO)
        * ===================================================================
        */
        :root {
            --sidebar-width: 260px;
            --topbar-height: 60px; /* Altura da barra superior no mobile */
        }
        
        body {
            background-color: hsl(var(--background));
            color: hsl(var(--foreground));
            min-height: 100vh;
        }
        
        /* --- Sidebar (Menu Lateral Esquerdo) --- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: hsl(var(--card)); /* Fundo do menu (branco no light mode) */
            border-right: 1px solid hsl(var(--border));
            padding: 1.5rem 1rem;
            transition: left 0.3s ease-in-out;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: hsl(var(--primary)); /* Cor primária (azul/preto no light mode) */
            text-decoration: none;
            padding: 0.5rem 0;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        /* Estilização dos links do menu */
        .sidebar .nav-link {
            color: hsl(var(--muted-foreground)); /* Cor do texto "apagado" */
            text-decoration: none;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem; /* rounded-md */
            transition: all 0.2s;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active,
        .sidebar .nav-link[aria-expanded="true"] {
            color: hsl(var(--foreground)); /* Cor do texto principal */
            background-color: hsl(var(--accent)); /* Fundo de hover/ativo */
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        
        /* Estilos do Dropdown (Bootstrap) */
        .sidebar .dropdown-menu {
            position: static !important;
            border: none !important;
            box-shadow: none !important;
            background-color: transparent !important;
            padding: 0;
            margin: 0;
        }
        .sidebar .dropdown-item {
            color: hsl(var(--muted-foreground));
            padding: 0.5rem 1rem 0.5rem 3rem; /* Indentação */
            font-size: 0.9rem;
            border-radius: 0.375rem;
        }
         .sidebar .dropdown-item:hover,
         .sidebar .dropdown-item.active {
            background-color: hsl(var(--accent));
            color: hsl(var(--foreground));
         }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid hsl(var(--border));
        }

        /* --- Conteúdo Principal --- */
        .main-content {
            transition: margin-left 0.3s ease-in-out;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
            /* Garante que o conteúdo use o fundo correto */
            background-color: hsl(var(--background));
            min-height: 100vh;
        }

        /* --- Header Mobile (para o botão de toggle) --- */
        .mobile-header {
            display: none; /* Oculto no desktop */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--topbar-height);
            background-color: hsl(var(--card));
            border-bottom: 1px solid hsl(var(--border));
            align-items: center;
            padding: 0 1rem;
            z-index: 1020;
            justify-content: space-between;
        }
        #sidebarToggle {
            background: none;
            border: none;
            color: hsl(var(--foreground));
            font-size: 1.5rem;
        }

        /* --- Rodapé --- */
        .footer-main {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease-in-out;
            background-color: hsl(var(--card));
            border-top: 1px solid hsl(var(--border));
            color: hsl(var(--muted-foreground));
            padding: 1.5rem;
            text-align: center;
        }
        
        /* --- Responsividade --- */
        @media (max-width: 992px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width)); /* Escondido por padrão */
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding-top: calc(var(--topbar-height) + 2rem); /* Espaço para o header mobile */
            }
            .footer-main {
                margin-left: 0;
            }
            .mobile-header {
                display: flex;
            }
            body.sidebar-open .sidebar {
                left: 0; /* Mostra o sidebar */
            }
            /* Overlay para fechar o menu no mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0; width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1029;
            }
            body.sidebar-open .sidebar-overlay {
                display: block;
            }
        }


        /*
        * ===================================================================
        * ESTILOS DE COMPATIBILIDADE (Bootstrap -> Tema)
        * ===================================================================
        */
        .card {
            background-color: hsl(var(--card));
            color: hsl(var(--card-foreground));
            border: 1px solid hsl(var(--border));
            border-radius: var(--radius);
        }
        .card-header {
            background-color: hsl(var(--card));
            border-bottom: 1px solid hsl(var(--border));
            color: hsl(var(--card-foreground));
        }
        
        .form-control, .form-select {
            background-color: hsl(var(--input));
            border-color: hsl(var(--border));
            color: hsl(var(--foreground));
        }
        .form-control:focus, .form-select:focus {
            background-color: hsl(var(--input));
            color: hsl(var(--foreground));
            border-color: hsl(var(--ring));
            box-shadow: none;
        }

        .table {
            --bs-table-color: hsl(var(--foreground));
            --bs-table-border-color: hsl(var(--border));
            --bs-table-striped-bg: hsl(var(--secondary));
            --bs-table-hover-bg: hsl(var(--accent));
        }
        .table-dark {
            --bs-table-color: hsl(var(--primary-foreground));
            --bs-table-bg: hsl(var(--primary));
            --bs-table-border-color: hsl(var(--border));
        }
        
        .modal-content {
             background-color: hsl(var(--card));
             color: hsl(var(--card-foreground));
             border: 1px solid hsl(var(--border));
         }
        .modal-header {
            border-bottom: 1px solid hsl(var(--border));
            color: hsl(var(--card-foreground));
        }
        .btn-close {
             filter: none; /* Remove o filtro branco do bootstrap padrão */
        }
        .dark .btn-close {
             filter: invert(1) grayscale(100%) brightness(200%); /* Adiciona o filtro apenas no dark mode */
        }

        /* Classes de utilitários do seu CSS (para os cards do dashboard) */
        .rounded-lg { border-radius: var(--radius); }
        .border { border-width: 1px; }
        .bg-card { background-color: hsl(var(--card)); }
        .text-card-foreground { color: hsl(var(--card-foreground)); }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgb(0 0 0 / .05); }
        .border-l-4 { border-left-width: 4px; }
        .border-l-green-500 { border-left-color: #22c55e; }
        .border-l-blue-500 { border-left-color: #3b82f6; }
        .border-l-purple-500 { border-left-color: #a855f7; }
        .border-l-orange-500 { border-left-color: #f97316; }
        .p-6 { padding: 1.5rem; }
        .flex { display: flex; }
        .flex-row { flex-direction: row; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .space-y-0 > :not([hidden]) ~ :not([hidden]) { margin-top: 0; margin-bottom: 0; }
        .pb-2 { padding-bottom: 0.5rem; }
        .tracking-tight { letter-spacing: -0.025em; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .font-medium { font-weight: 500; }
        .text-muted-foreground { color: hsl(var(--muted-foreground)); }
        .h-4 { height: 1rem; }
        .w-4 { width: 1rem; }
        .text-green-600 { color: #16a34a; }
        .text-blue-600 { color: #2563eb; }
        .text-purple-600 { color: #9333ea; }
        .text-orange-600 { color: #ea580c; }
        .pt-0 { padding-top: 0; }
        .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        .font-bold { font-weight: 700; }
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .mt-1 { margin-top: 0.25rem; }
        .grid { display: grid; }
        .gap-6 { gap: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
        .from-blue-600 { --tw-gradient-from: #2563eb; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(37, 99, 235, 0)); }
        .to-indigo-600 { --tw-gradient-to: #4f46e5; }
        .bg-clip-text { -webkit-background-clip: text; background-clip: text; }
        .text-transparent { color: transparent; }
        .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
        
        /* Botões Bootstrap */
        .btn-primary { 
            background-color: hsl(var(--primary)); 
            border-color: hsl(var(--primary));
            color: hsl(var(--primary-foreground));
        }
        .btn-primary:hover {
            background-color: hsl(var(--primary) / 0.9);
            border-color: hsl(var(--primary) / 0.9);
        }
        .btn-success {
            background-color: #16a34a; /* green-600 */
            border-color: #16a34a;
            color: #ffffff;
        }
        .btn-success:hover {
            background-color: #15803d; /* green-700 */
            border-color: #15803d;
        }
        .btn-danger {
            background-color: hsl(var(--destructive));
            border-color: hsl(var(--destructive));
            color: hsl(var(--destructive-foreground));
        }
         .btn-danger:hover {
            background-color: hsl(var(--destructive) / 0.9);
            border-color: hsl(var(--destructive) / 0.9);
         }
        .btn-warning {
             background-color: #f97316; /* orange-500 */
             border-color: #f97316;
             color: #ffffff;
         }
        .alert-info {
            color: hsl(var(--accent-foreground));
            background-color: hsl(var(--accent));
            border-color: hsl(var(--border));
        }
        .alert-warning {
            color: #9a3412; /* orange-900 */
            background-color: #ffedd5; /* orange-100 */
            border-color: #fdba74; /* orange-300 */
        }
        .dark .alert-warning {
            color: hsl(var(--destructive-foreground));
            background-color: hsl(var(--destructive));
            border-color: hsl(var(--destructive) / 0.5);
        }
    </style>
</head>
<body class="light"> <!-- Classe 'light' ou 'dark' aqui para forçar o tema -->

<div id="sidebarOverlay" class="sidebar-overlay"></div>

<div class="sidebar">
    <a class="sidebar-brand" href="index.php">
        <i class="fas fa-chart-pie"></i>
        Dash CPA
    </a>
    
    <!-- ul.nav.flex-column é do Bootstrap e é necessário para os dropdowns funcionarem -->
    <ul class="nav flex-column">
        <?php if (isset($_SESSION['role'])): ?>
            
            <?php // === MENU USUÁRIO ===
            if ($_SESSION['role'] == 'usuario'): ?>
                <li class="nav-item"> <a class="nav-link active" href="dashboard_usuario.php"><i class="fas fa-home"></i> Meu Painel</a> </li>
            
            <?php // === MENU GERENTE (Admin / Sub-Admin) ===
            elseif (in_array($_SESSION['role'], ['admin', 'sub_adm'])): ?>
                <li class="nav-item"> <a class="nav-link" href="dashboard_admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a> </li>
                
                <!-- Dropdown Gerenciamento -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menu-gerenciamento" role="button" aria-expanded="false" aria-controls="menu-gerenciamento">
                        <i class="fas fa-users-cog"></i> Gerenciamento <i class="fas fa-chevron-down fa-xs ms-auto"></i>
                    </a>
                    <div class="collapse" id="menu-gerenciamento">
                        <ul class="nav flex-column dropdown-menu sidebar-dropdown">
                            <li><a class="dropdown-item nav-link" href="create_user.php"><i class="fas fa-user-plus fa-xs"></i> Criar Conta</a></li>
                            <li><a class="dropdown-item nav-link" href="manage_users.php"><i class="fas fa-users fa-xs"></i> Gerenciar Usuários</a></li>
                        </ul>
                    </div>
                </li>
                
                <!-- Dropdown Relatórios -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menu-relatorios" role="button" aria-expanded="false" aria-controls="menu-relatorios">
                        <i class="fas fa-chart-line"></i> Relatórios <i class="fas fa-chevron-down fa-xs ms-auto"></i>
                    </a>
                    <div class="collapse" id="menu-relatorios">
                         <ul class="nav flex-column dropdown-menu sidebar-dropdown">
                            <!-- ATUALIZADO AQUI -->
                            <li><a class="dropdown-item nav-link" href="daily_control.php"><i class="fas fa-calendar-day fa-xs"></i> Controle Diário</a></li>
                            <li><a class="dropdown-item nav-link" href="reports.php"><i class="fas fa-file-alt fa-xs"></i> Detalhados</a></li>
                            <li><a class="dropdown-item nav-link" href="saved_reports.php"><i class="fas fa-save fa-xs"></i> Salvos</a></li>
                         </ul>
                    </div>
                </li>

            <?php // === MENU SUPER ADMIN ===
            elseif ($_SESSION['role'] == 'super_adm'): ?>
                <li class="nav-item"> <a class="nav-link" href="dashboard_superadmin.php"><i class="fas fa-crown"></i> Dashboard</a> </li>
                
                <!-- Dropdown Gerenciamento -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menu-gerenciamento" role="button" aria-expanded="false" aria-controls="menu-gerenciamento">
                        <i class="fas fa-users-cog"></i> Gerenciamento <i class="fas fa-chevron-down fa-xs ms-auto"></i>
                    </a>
                    <div class="collapse" id="menu-gerenciamento">
                         <ul class="nav flex-column dropdown-menu sidebar-dropdown">
                            <li><a class="dropdown-item nav-link" href="create_user.php"><i class="fas fa-user-plus fa-xs"></i> Criar Conta</a></li>
                            <li><a class="dropdown-item nav-link" href="manage_users.php"><i class="fas fa-users fa-xs"></i> Gerenciar Usuários</a></li>
                            <li><a class="dropdown-item nav-link" href="manage_subadmins.php"><i class="fas fa-user-shield fa-xs"></i> Gerenciar Admins</a></li>
                         </ul>
                    </div>
                </li>
                
                <!-- Dropdown Relatórios -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#menu-relatorios" role="button" aria-expanded="false" aria-controls="menu-relatorios">
                        <i class="fas fa-chart-line"></i> Relatórios <i class="fas fa-chevron-down fa-xs ms-auto"></i>
                    </a>
                    <div class="collapse" id="menu-relatorios">
                         <ul class="nav flex-column dropdown-menu sidebar-dropdown">
                            <!-- ATUALIZADO AQUI -->
                            <li><a class="dropdown-item nav-link" href="daily_control.php"><i class="fas fa-calendar-day fa-xs"></i> Controle Diário</a></li>
                            <li><a class="dropdown-item nav-link" href="reports.php"><i class="fas fa-file-alt fa-xs"></i> Detalhados</a></li>
                            <li><a class="dropdown-item nav-link" href="saved_reports.php"><i class="fas fa-save fa-xs"></i> Salvos</a></li>
                            <li><a class="dropdown-item nav-link" href="view_logs.php"><i class="fas fa-clipboard-list fa-xs"></i> Logs do Sistema</a></li>
                         </ul>
                    </div>
                </li>

            <?php endif; ?>

            <!-- Menu de Logout (no rodapé do sidebar) -->
            <div class="sidebar-footer">
                <ul class="nav flex-column">
                    <li class="nav-item"> <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a> </li>
                </ul>
            </div>

        <?php else: // Não está logado ?>
            <li class="nav-item"> <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a> </li>
        <?php endif; ?>
    </ul>
</div>

<!-- Header Mobile (Apenas para o botão) -->
<div class="mobile-header">
    <a class="sidebar-brand" href="index.php" style="margin-bottom: 0; padding: 0;">CPA Control</a>
    <button id="sidebarToggle"><i class="fas fa-bars"></i></button>
</div>

<!-- Conteúdo Principal (Aberto aqui, fechado no footer.php) -->
<div class="main-content container-fluid">