<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | BunnyKlin</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            font-family: 'Nunito', sans-serif !important;
            background-color: #F4F8FC !important; /* Azul muy pálido para el fondo */
            color: #1E55AA !important; /* Main Blue Oficial */
        }
        
        body.dark {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        /* --- ESTILOS DEL MENÚ LATERAL --- */
        .menu-item {
            border-radius: 0.75rem !important; 
            margin-bottom: 0.35rem !important;
            font-weight: 700 !important;
            transition: all 0.2s ease !important;
        }
        
        /* Items inactivos */
        .menu-item-inactive {
            color: #D1E4F9 !important; 
        }
        
        .menu-item-inactive:hover {
            background-color: rgba(255, 255, 255, 0.1) !important; 
            color: #FFFFFF !important; 
        }
        
        /* Item Activo */
        .menu-item-active {
            background-color: #FFFFFF !important; 
            color: #1E55AA !important; /* Main Blue Oficial */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Iconos del menú */
        .menu-item-icon-inactive {
            color: #7DA6D4 !important; 
        }
        .menu-item-inactive:hover .menu-item-icon-inactive {
            color: #FFFFFF !important; 
        }
        .menu-item-icon-active {
            color: #1E55AA !important; /* Main Blue Oficial */
        }
        
        /* Ocultar flechas de inputs numéricos */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const savedTheme = localStorage.getItem('theme');
                    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = savedTheme || systemTheme;
                    this.updateTheme();
                },
                theme: 'light',
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    if (this.theme === 'dark') {
                        html.classList.add('dark');
                        body.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                        body.classList.remove('dark');
                    }
                }
            });

            Alpine.store('sidebar', {
                isExpanded: window.innerWidth >= 1280,
                isMobileOpen: false,
                isHovered: false,

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    this.isMobileOpen = false;
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                },

                setMobileOpen(val) {
                    this.isMobileOpen = val;
                },

                setHovered(val) {
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = val;
                    }
                }
            });
        });
    </script>
</head>

<body
    x-data="{ 'loaded': true}"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);">

    {{-- ¡AQUÍ ESTÁ LA MAGIA! El script ahora corre cuando el body YA EXISTE --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.body.classList.remove('dark');
            }
        })();
    </script>

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    <div class="min-h-screen xl:flex selection:bg-[#FFE63C] selection:text-[#1E55AA]">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            @include('layouts.app-header')
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 relative z-10">
                @yield('content')
            </div>
        </div>

    </div>

</body>

@stack('scripts')

</html>