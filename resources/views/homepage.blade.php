<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dahana Rekayas Nusantara | Industrial Manufacturing Solutions</title>
    <meta name="description" content="Integrated solutions for modern manufacturing industry with premium products and services.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ffffff',  // White as major color
                        secondary: '#000000', // Black as minor color
                        accent: '#ff0000',    // Red as minor color
                        eco: '#00ff00',       // Green as minor color
                        dark: '#1f2937',
                        light: '#f9fafb'
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-reverse': 'float-reverse 5s ease-in-out infinite',
                        'pulse-slow': 'pulse 5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'wave': 'wave 8s linear infinite',
                        'mechanical-arm': 'mechanical-arm 4s ease-in-out infinite alternate',
                        'welder-spark': 'welder-spark 2s ease-out infinite'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        'float-reverse': {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(15px)' }
                        },
                        wave: {
                            '0%': { transform: 'rotate(0deg)' },
                            '10%': { transform: 'rotate(-5deg)' },
                            '20%': { transform: 'rotate(10deg)' },
                            '30%': { transform: 'rotate(-5deg)' },
                            '40%': { transform: 'rotate(10deg)' },
                            '50%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(0deg)' }
                        },
                        'mechanical-arm': {
                            '0%': { transform: 'rotate(-5deg)' },
                            '100%': { transform: 'rotate(5deg)' }
                        },
                        'welder-spark': {
                            '0%': { opacity: '0', transform: 'scale(0.5)' },
                            '50%': { opacity: '1', transform: 'scale(1.2)' },
                            '100%': { opacity: '0', transform: 'scale(0.5)' }
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- GSAP for advanced animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preload" href="/api/products" as="fetch" crossorigin="anonymous">
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            scroll-behavior: smooth;
            background-color: #f9fafb;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.9) 0%, rgba(255, 0, 0, 0.8) 100%);
        }
        
        .hero-section {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        
        .product-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .product-card:hover::before {
            opacity: 1;
        }
        
        .card-hover-effect {
            transition: all 0.3s ease;
        }
        
        .card-hover-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .skema-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }
        
        .skema-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: #ff0000;
        }
        
        .skema-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #000000, #ff0000);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .skema-card:hover::after {
            transform: scaleX(1);
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #ff0000;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }

        .merah-gradient-background {
            background: linear-gradient(135deg, #000000, #ff0000);
        }

        .merah-gradient-text {
            background: linear-gradient(135deg, #000000, #ff0000);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #000000, #ff0000);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        
        .btn-outline {
            transition: all 0.3s ease;
            border: 2px solid #ff0000;
        }
        
        .btn-outline:hover {
            background-color: #ff0000;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
        }
        
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #000000, #ff0000);
            margin: 12px auto 0;
            border-radius: 2px;
        }
        
        .floating-shape {
            position: absolute;
            opacity: 0.1;
            z-index: 0;
        }
        
        .shape-1 {
            top: 20%;
            left: 5%;
            animation: float 8s ease-in-out infinite;
        }
        
        .shape-2 {
            bottom: 15%;
            right: 8%;
            animation: float-reverse 7s ease-in-out infinite;
        }
        
        .shape-3 {
            top: 40%;
            right: 10%;
            animation: float 9s ease-in-out infinite;
        }
        
        .mechanical-arm {
            position: relative;
            animation: mechanical-arm 4s ease-in-out infinite alternate;
            transform-origin: bottom center;
        }
        
        .welder-spark {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #f59e0b;
            border-radius: 50%;
            filter: blur(2px);
            animation: welder-spark 2s ease-out infinite;
        }
        
        .masked-image {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C150,200 350,0 500,100 L500,500 L0,500 Z' fill='%23000'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C150,200 350,0 500,100 L500,500 L0,500 Z' fill='%23000'/%3E%3C/svg%3E");
            -webkit-mask-position: center;
            mask-position: center;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-size: cover;
            mask-size: cover;
        }
        
        .curve-divider {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            transform: rotate(180deg);
        }
        
        .curve-divider svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 100px;
        }
        
        .curve-divider .shape-fill {
            fill: #FFFFFF;
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.1) 0%, rgba(255, 0, 0, 0.1) 100%);
            border-radius: 50%;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: rotateY(180deg);
            background: linear-gradient(135deg, #000000, #ff0000);
            color: white;
        }
        
        .company-logo {
            transition: all 0.3s ease;
        }
        
        .company-logo:hover {
            opacity: 1;
            transform: scale(1.05);
        }
        
        .floating-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #ff0000;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
            animation: pulse-slow 3s infinite;
        }
        
        .testimonial-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .testimonial-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #000000, #ff0000);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-card:hover::before {
            transform: scaleX(1);
        }
        
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 50px;
            border: 2px solid white;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .scroll-indicator::before {
            content: '';
            position: absolute;
            top: 8px;
            width: 6px;
            height: 6px;
            background-color: white;
            border-radius: 50%;
            animation: scroll-down 2s infinite;
        }
        
        @keyframes scroll-down {
            0% {
                transform: translateY(0);
                opacity: 1;
            }
            50% {
                transform: translateY(10px);
                opacity: 0.5;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .grid-pattern {
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        
        .floating-keywords {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 20px;
            z-index: 2;
        }
        
        .keyword-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 10px 15px;
            border-radius: 30px;
            color: white;
            font-weight: 600;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.5s ease;
            cursor: default;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .keyword-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(0) scale(1.05);
        }
        
        .keyword-item i {
            margin-right: 8px;
        }
        .product-card {
    position: relative;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.product-card:hover {
    border-color: #ff0000;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.product-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #000000, #ff0000);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}

.product-card:hover::after {
    transform: scaleX(1);
}

/* Animation for product cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-card {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.3s; }
.product-card:nth-child(3) { animation-delay: 0.5s; }

[x-cloak] { display: none !important; }

/* Tambahkan ini di bagian style */
.section-with-bg {
            background-image: url('{{ asset('img/sketch.jpeg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        
        .section-with-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.85); /* Overlay putih dengan opacity 85% */
        }
        
        .section-with-bg > * {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="section-with-bg" x-data="{ openModal: null }">
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50" x-data="{ open: false }">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('img/logo-auto.png') }}" alt="Ghava Shankara Nusantara" class="h-10 object-contain">
                </a>
            </div>
            
            <div class="hidden md:flex space-x-8">
                <a href="#home" class="text-gray-800 hover:merah-gradient-text font-medium nav-link">Beranda</a>
                <a href="#products" class="text-gray-800 hover:merah-gradient-text font-medium nav-link">Produk</a>
                <a href="#features" class="text-gray-800 hover:merah-gradient-text font-medium nav-link">Keunggulan</a>
                <a href="#about" class="text-gray-800 hover:merah-gradient-text font-medium nav-link">Tentang Kami</a>
                <a href="#companies" class="text-gray-800 hover:merah-gradient-text font-medium nav-link">Perusahaan</a>
                <a href="{{ url('/login') }}" class="bg-primary text-white hover:bg-secondary font-medium py-2 px-4 rounded-full transition duration-300">Masuk Syirkah</a>
            </div>
            
            <button @click="open = !open" class="md:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden bg-white shadow-lg">
            <div class="px-4 py-2 space-y-2">
                <a href="#home" class="block py-2 text-gray-800 hover:merah-gradient-text">Beranda</a>
                <a href="#products" class="block py-2 text-gray-800 hover:merah-gradient-text">Produk</a>
                <a href="#features" class="block py-2 text-gray-800 hover:merah-gradient-text">Keunggulan</a>
                <a href="#about" class="block py-2 text-gray-800 hover:text-primary">Tentang Kami</a>
                <a href="#companies" class="block py-2 text-gray-800 hover:text-primary">Perusahaan</a>
                <a href="{{ url('/login') }}" class="bg-primary text-white block py-2 text-primary font-medium">Masuk Syirkah</a>
            </div>
        </div>
    </nav>

    <!-- Futuristic Hero Section -->
    <section id="home" class="relative h-[90vh] overflow-hidden bg-gray-900">
        <!-- Futuristic Border Mask -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <div class="futuristic-mask absolute inset-0">
                <!-- Slide Show -->
                <div x-data="{
                    currentSlide: 0,
                    slides: [
                        {
                            image: '{{ asset('img/slideshow/slide1.jpg') }}',
                            title: 'Teknologi Miniboiler Hemat Energi',
                            subtitle: 'Solusi tepat untuk kebutuhan UMKM dengan penghematan bahan bakar hingga 85%'
                        },
                        {
                            image: '{{ asset('img/slideshow/slide2.jpg') }}',
                            title: 'Biomass Ramah Lingkungan',
                            subtitle: 'Bahan bakar terbarukan untuk operasi yang berkelanjutan'
                        },
                        {
                            image: '{{ asset('img/products/penggorengan.jpg') }}',
                            title: 'UKM Green Tech Solution',
                            subtitle: 'Nikmati teknologi tanpa investasi besar'
                        }
                    ],
                    init() {
                        setInterval(() => {
                            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                        }, 5000);
                    }
                }" class="h-full w-full">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="currentSlide === index" 
                            x-transition:enter="transition ease-out duration-1000"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-1000"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0">
                            <img :src="slide.image" 
                                :alt="slide.title" 
                                class="w-full h-full object-cover object-center">
                            <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-transparent opacity-80"></div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Circuit Pattern Overlay -->
            <div class="circuit-pattern absolute inset-0 opacity-10"></div>
        </div>

        <!-- Content -->
        <div class="container mx-auto px-4 h-full flex items-center relative z-10">
            <div class="max-w-2xl">
                <div x-data="{ 
                    currentSlide: 0,
                    slides: [
                        'Teknologi Miniboiler Hemat Energi',
                        'Biomass Ramah Lingkungan',
                        'UKM Green Tech Solution'
                    ]
                }">
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-4" x-text="slides[currentSlide]"></h1>
                </div>
                <p class="text-xl md:text-2xl text-gray-300 mb-8">Solusi tepat guna untuk UMKM dengan penghematan bahan bakar hingga 85%</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#products" class="btn-primary bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300">
                        Lihat Produk
                    </a>
                    <a href="#videos" class="btn-primary bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full text-lg transition duration-300">
                        Video Produk
                    </a>
                    <a href="https://wa.me/62812220033?text=Selamat%20Pagi%20PT.%20Dahana%20Rekayasa%20Nusantara%2C%20saya%20memperoleh%20informasi%20mengenai%20perusahaan%20ini%20dari%20website%2C%20saya%20ingin%20menanyakan%20lebih%20lanjut%20terkait%20produk%20di%20perusahaan%20ini." 
                        target="_blank" class="btn-outline border-2 border-white text-white hover:bg-white hover:text-gray-900 font-bold py-3 px-8 rounded-full text-lg transition duration-300">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide Indicators -->
        <div class="absolute bottom-8 left-0 right-0 flex justify-center space-x-2 z-10">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="currentSlide = index" 
                        class="w-3 h-3 rounded-full transition-colors duration-300 focus:outline-none"
                        :class="{'bg-white': currentSlide === index, 'bg-gray-500': currentSlide !== index}">
                </button>
            </template>
        </div>

        <!-- Tech Badges -->
        <div class="absolute right-8 bottom-8 hidden lg:block">
            <div class="flex flex-col gap-3">
                <div @click="openModal = 'hemat'" class="tech-badge cursor-pointer">
                    <i class="fas fa-bolt text-red-400"></i>
                    <span>Hemat Energi</span>
                </div>
                <div @click="openModal = 'portabel'" class="tech-badge cursor-pointer">
                    <i class="fas fa-truck-moving text-red-400"></i>
                    <span>Portabel</span>
                </div>
                <div @click="openModal = 'powerfull'" class="tech-badge cursor-pointer">
                    <i class="fas fa-fire text-red-400"></i>
                    <span>Powerfull</span>
                </div>
                <div @click="openModal = 'eco'" class="tech-badge cursor-pointer">
                    <i class="fas fa-leaf text-red-400"></i>
                    <span>Ramah Lingkungan</span>
                </div>
            </div>
        </div>
    </section>

    <style>
        .futuristic-mask {
            -webkit-mask-image: url("data:image/svg+xml,%3Csvg width='500' height='500' viewBox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,0 L500,0 L500,400 C400,450 300,400 200,450 C100,500 50,450 0,400 L0,0 Z' fill='black'/%3E%3C/svg%3E");
            mask-image: url("data:image/svg+xml,%3Csvg width='500' height='500' viewBox='0 0 500 500' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,0 L500,0 L500,400 C400,450 300,400 200,450 C100,500 50,450 0,400 L0,0 Z' fill='black'/%3E%3C/svg%3E");
            -webkit-mask-position: center;
            mask-position: center;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-size: cover;
            mask-size: cover;
        }
        
        .circuit-pattern {
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(59, 130, 246, 0.3) 1px, transparent 0),
                radial-gradient(circle at 1px 1px, rgba(59, 130, 246, 0.3) 1px, transparent 0);
            background-size: 20px 20px;
        }
        
        .tech-badge {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 8px 15px;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .tech-badge:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }

        
    </style>

    <!-- Tentang Kami Section -->
    <section id="about" class="py-16">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12">
            <div class="lg:w-3/4" data-aos="fade-right">
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">Tentang <span class=" merah-gradient-text text-red-600">Dahana</span></h2>
                    <div class="w-20 h-1 bg-red-600 mb-6"></div>

                    <p class="text-gray-600 mb-4">
                        Kami adalah perusahaan manufaktur teknologi tepat guna "Miniboiler" dan "biomassa" bahan bakar ramah lingkungan, yang fokus memenuhi kebutuhan teknologi UMKM.
                    </p>
                    <p class="text-gray-600 mb-4">
                        Perjalanan kami dimulai pada tahun 2007, ketika riset tentang miniboiler hemat energi pertama kali digagas di tengah lingkungan manufaktur permesinan. Semangat inovasi terus menyala bahkan setelah masa perkuliahan, mendorong kami mengembangkan produk berkualitas yang efisien dan ramah lingkungan.
                    </p>
                    <p class="text-gray-600 mb-4">
                        Pada tahun 2013, hasil riset tersebut berhasil diwujudkan dalam bentuk nyata: sebuah miniboiler yang tangguh, portable, dan ramah lingkungan. Tahun ini menjadi tonggak penting dengan berdirinya <strong>Dahana</strong>, yang secara resmi beroperasi secara legal sebagai badan usaha.
                    </p>
                    <p class="text-gray-600 mb-6">
                        Teknologi Dahana miniboiler terbukti hemat energi, ramah lingkungan dan user friendly dengan efisiensi bahan bakar hingga 85%.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-eye mr-2 text-red-600"></i> Visi
                            </h3>
                            <p class="text-gray-600">
                                Menjadi perusahaan yang terpercaya dan memberikan solusi teknologi berkualitas
                            </p>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-bullseye mr-2 text-red-600"></i> Misi
                            </h3>
                            <p class="text-gray-600">
                                Meningkatkan kesejahteraan yang berkelanjutan bagi seluruh pemangku kepentingan berdasarkan kepedulian dan tanggung jawab sosial
                            </p>
                        </div>
                    </div>
                </div>
            </div>
                
                <div class="lg:w-1/4" data-aos="fade-left">
                    <!-- Unit Bisnis -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Unit Bisnis Kami</h3>
                        <div class="space-y-4">
                            <div class="flex items-start p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                                <div class="bg-red-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-industry text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Manufaktur Miniboiler</h4>
                                    <p class="text-gray-600">Fokus produksi miniboiler dengan efisiensi bahan bakar hingga 85%, berbahan bakar ramah lingkungan dan mudah dioperasikan</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                                <div class="bg-red-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-tools text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Service & Spare Part</h4>
                                    <p class="text-gray-600">Teknisi ahli dan layanan purna jual yang mendukung kegiatan produksi UMKM</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                                <div class="bg-red-100 p-3 rounded-full mr-4">
                                    <i class="fas fa-leaf text-red-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Renewable Energy</h4>
                                    <p class="text-gray-600">Pembuatan dan penjualan bahan bakar ramah lingkungan Biomass</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<style>
@keyframes spin-slow { 0%{transform:rotate(0)} 100%{transform:rotate(360deg)} }
.animate-spin-slow { animation: spin-slow 22s linear infinite; }

@keyframes floaty { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
.animate-floaty { animation: floaty 3s ease-in-out infinite; }

@keyframes glow { 0%,100%{ box-shadow: 0 0 0 rgba(239,68,68,0) } 50%{ box-shadow: 0 0 40px rgba(239,68,68,.35) } }
.animate-glow { animation: glow 3s ease-in-out infinite; }

@keyframes shimmer { 0%{ transform: translateX(-100%) } 100%{ transform: translateX(100%) } }
.shimmer { position: relative; overflow: hidden; }
.shimmer::after {
  content:""; position:absolute; inset:-40%  -80%; 
  background: linear-gradient(120deg,transparent 20%,rgba(255,255,255,.6) 50%,transparent 80%);
  animation: shimmer 2.6s linear infinite; pointer-events:none;
}
</style>

<!-- Prestasi Section-->
<section id="achievements" class="relative py-24 overflow-hidden">
  <!-- Background ornaments -->
  <div class="pointer-events-none absolute -top-10 -left-10 w-48 h-48 bg-red-200/40 rounded-full blur-3xl animate-pulse"></div>
  <div class="pointer-events-none absolute bottom-0 right-0 w-72 h-72 bg-red-300/30 rounded-full blur-3xl animate-spin-slow"></div>

  <div class="container mx-auto px-6 relative z-10">
    <div class="text-center mb-20">
      <h2 class="text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">
        Prestasi <span class="text-red-600">Kami</span>
      </h2>
      <p class="text-lg text-gray-700 max-w-2xl mx-auto">
        Perjalanan kami dalam inovasi, energi, dan pengakuan nasional
      </p>
      <div class="w-28 h-1 bg-red-600 mx-auto mt-6 rounded-full"></div>
    </div>

    <div class="grid gap-12 md:grid-cols-3">
      <!-- Card 2014 -->
      <div class="bg-white rounded-3xl p-8 shadow-2xl hover:shadow-red-300 transition duration-500 transform hover:scale-105 relative" data-aos="fade-up">
        <div class="absolute -top-5 -left-5 bg-red-600 text-white text-sm font-bold px-4 py-1 rounded-full shadow">
          2014
        </div>

        <!-- Big Trophy -->
        <div class="relative flex justify-center mb-8">
          <div class="absolute h-24 w-24 rounded-full bg-yellow-400/20"></div>
          <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-yellow-300 via-yellow-400 to-amber-500 flex items-center justify-center shadow-xl animate-floaty">
            <i class="fas fa-trophy text-5xl text-white drop-shadow"></i>
          </div>
        </div>

        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Prestasi Nasional</h3>
        <ul class="space-y-4">
            <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="150">
                <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="absolute -inset-1 rounded-2xl bg-red-400/20"></span>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:-rotate-3 group-hover:scale-105 transition">
                    <i class="fas fa-award text-3xl text-white"></i>
                    </div>
                </div>
                <p class="text-lg font-semibold text-gray-800">Juara 1 Kompetisi Hijau INOTEK</p>
                </div>
            </li>
            <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="150">
                <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="absolute -inset-1 rounded-2xl bg-red-400/20"></span>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:-rotate-3 group-hover:scale-105 transition">
                    <i class="fas fa-award text-3xl text-white"></i>
                    </div>
                </div>
                <p class="text-lg font-semibold text-gray-800">Juara 1 Mandiri Young Technopreneur</p>
                </div>
            </li>
        </ul>
      </div>

      <!-- Card 2015 -->
      <div class="bg-white rounded-3xl p-8 shadow-2xl hover:shadow-red-300 transition duration-500 transform hover:scale-105 relative"
           data-aos="fade-up" data-aos-delay="100">
        <div class="absolute -top-5 -left-5 bg-red-600 text-white text-sm font-bold px-4 py-1 rounded-full shadow">
          2015
        </div>

        <div class="relative flex justify-center mb-8">
          <div class="absolute h-24 w-24 rounded-full bg-yellow-400/20"></div>
          <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-yellow-300 via-yellow-400 to-amber-500 flex items-center justify-center shadow-xl animate-floaty">
            <i class="fas fa-trophy text-5xl text-white drop-shadow"></i>
          </div>
        </div>

        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Prestasi Nasional</h3>
        <ul class="space-y-4">
          <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="150">
            <div class="flex items-center gap-4">
              <div class="relative">
                <span class="absolute -inset-1 rounded-2xl bg-red-400/20"></span>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:-rotate-3 group-hover:scale-105 transition">
                  <i class="fas fa-medal text-3xl text-white"></i>
                </div>
              </div>
              <p class="text-lg font-semibold text-gray-800">Penghargaan Gubernur Jawa Barat</p>
            </div>
          </li>

          <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="200">
            <div class="flex items-center gap-4">
              <div class="relative">
                <span class="absolute -inset-1 rounded-2xl bg-red-400/20"></span>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:rotate-3 group-hover:scale-105 transition">
                  <i class="fas fa-medal text-3xl text-white"></i>
                </div>
              </div>
              <p class="text-lg font-semibold text-gray-800">Juara 1 Anugerah Inovasi Jawa Barat</p>
            </div>
          </li>

          <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="250">
            <div class="flex items-center gap-4">
              <div class="relative">
                <span class="absolute -inset-1 rounded-2xl bg-red-400/20"></span>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:-rotate-3 group-hover:scale-105 transition">
                  <i class="fas fa-medal text-3xl text-white"></i>
                </div>
              </div>
              <p class="text-lg font-semibold text-gray-800">Penghargaan dari Kepresidenan RI</p>
            </div>
          </li>
        </ul>
      </div>

      <!-- Card 2016 -->
      <div class="bg-white rounded-3xl p-8 shadow-2xl hover:shadow-red-300 transition duration-500 transform hover:scale-105 relative"
           data-aos="fade-up" data-aos-delay="200">
        <div class="absolute -top-5 -left-5 bg-red-600 text-white text-sm font-bold px-4 py-1 rounded-full shadow">
          2016
        </div>

        <div class="relative flex justify-center mb-8">
          <div class="absolute h-24 w-24 rounded-full bg-yellow-400/20"></div>
          <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-yellow-300 via-yellow-400 to-amber-500 flex items-center justify-center shadow-xl animate-floaty">
            <i class="fas fa-trophy text-5xl text-white drop-shadow"></i>
          </div>
        </div>

        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Prestasi Nasional</h3>
        <ul class="space-y-4">
          <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="230">
            <div class="flex items-center gap-4">
              <div class="relative">
                <span class="absolute -inset-1 rounded-2xl bg-amber-400/20"></span>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:-rotate-3 group-hover:scale-105 transition">
                  <i class="fas fa-bolt text-3xl text-white"></i>
                </div>
              </div>
              <p class="text-lg font-semibold text-gray-800">Penghargaan Energi Pratama ESDM</p>
            </div>
          </li>

          <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="280">
            <div class="flex items-center gap-4">
              <div class="relative">
                <span class="absolute -inset-1 rounded-2xl bg-amber-400/20"></span>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:-rotate-3 group-hover:scale-105 transition">
                  <i class="fas fa-bolt text-3xl text-white"></i>
                </div>
              </div>
              <p class="text-lg font-semibold text-gray-800">Kategori Best of the Best</p>
            </div>
          </li>

          <li class="group shimmer bg-white/80 backdrop-blur rounded-2xl p-5 border border-red-100 hover:border-red-300 transition transform hover:-translate-y-1"
              data-aos="zoom-in" data-aos-delay="330">
            <div class="flex items-center gap-4">
              <div class="relative">
                <span class="absolute -inset-1 rounded-2xl bg-amber-400/20"></span>
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-500 flex items-center justify-center shadow-lg group-hover:-rotate-3 group-hover:scale-105 transition">
                  <i class="fas fa-bolt text-3xl text-white"></i>
                </div>
              </div>
              <p class="text-lg font-semibold text-gray-800">The Most Inspiring</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>

    <!-- Media Section -->
    <section class="py-16" x-data="{ showModal: false, modalType: '', modalSrc: '' }">
        <div class="container mx-auto px-4">
            <!-- Judul -->
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Fitur di <span class="merah-gradient-text text-red-600">Media</span>
                </h2>
                <div class="w-20 h-1 bg-red-600 mx-auto"></div>
            </div>

            <!-- Media Grid -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <!-- BIG BANG SHOW -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition duration-300 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fab fa-youtube text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg">BIG BANG SHOW</h3>
                    <p class="text-gray-500 text-sm">Metro TV</p>
                    <button 
                        @click="showModal=true; modalType='video'; modalSrc='https://www.youtube.com/embed/44MZSmT2BUA?autoplay=1'"
                        class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-500 transition">
                        Lihat Video
                    </button>
                </div>

                <!-- Laptop Si Unyil -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition duration-300 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tv text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg">Laptop Si Unyil</h3>
                    <p class="text-gray-500 text-sm">Trans 7</p>
                </div>

                <!-- Sang Kreator -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition duration-300 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tv text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg">Sang Kreator</h3>
                    <p class="text-gray-500 text-sm">TVRI</p>
                </div>

                <!-- Majalah Listrik Indonesia -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition duration-300 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-newspaper text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg">Majalah Listrik Indonesia</h3>
                    <p class="text-gray-500 text-sm">Majalah</p>
                    <button 
                        @click="showModal=true; modalType='image'; modalSrc='public/img/media/majalah.jpg'"
                        class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition">
                        Lihat Foto
                    </button>
                </div>

                <!-- Tambahan: Expo Energi 2024 -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition duration-300 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-bolt text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-lg">Tabloid Kontan</h3>
                    <p class="text-gray-500 text-sm">Tabloid</p>
                    <button 
                        @click="showModal=true; modalType='image'; modalSrc='public/img/media/expo.jpg'"
                        class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 transition">
                        Lihat Foto
                    </button>
                </div>
            </div>

            <!-- Social Media -->
            <div class="mt-16 text-center">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Ikuti Kami di Media Sosial</h3>
                <div class="flex justify-center gap-6 flex-wrap">
                    <a href="https://www.tiktok.com/@dahana.boiler?_t=ZS-8yENQkM5Dce&_r=1" target="_blank"
                        class="w-12 h-12 rounded-full bg-gray-800 hover:bg-gray-700 text-white flex items-center justify-center text-xl transition"
                        title="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>
                    <a href="https://www.instagram.com/yourusername" target="_blank"
                        class="w-12 h-12 rounded-full bg-pink-600 hover:bg-pink-500 text-white flex items-center justify-center text-xl transition"
                        title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.facebook.com/yourusername" target="_blank"
                        class="w-12 h-12 rounded-full bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center text-xl transition"
                        title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/yourusername" target="_blank"
                        class="w-12 h-12 rounded-full bg-blue-800 hover:bg-blue-700 text-white flex items-center justify-center text-xl transition"
                        title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="relative bg-white rounded-lg overflow-hidden max-w-3xl w-full">
                <button @click="showModal=false" class="absolute top-2 right-2 text-gray-700 hover:text-black text-2xl">&times;</button>
                <template x-if="modalType==='video'">
                    <iframe :src="modalSrc" class="w-full aspect-video" frameborder="0" allowfullscreen></iframe>
                </template>
                <template x-if="modalType==='image'">
                    <img :src="modalSrc" alt="Media" class="w-full h-auto">
                </template>
            </div>
        </div>
    </section>



    <!-- Keunggulan Teknologi Kami -->
    <section class="py-16 relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Keunggulan <span class=" merah-gradient-text text-red-600">Teknologi</span> Kami</h2>
                <div class="section-title"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Hemat Energi -->
                <div @click="openModal = 'hemat'" class="cursor-pointer bg-gray-50 p-8 rounded-xl text-center shadow hover:shadow-lg transition" data-aos="fade-up">
                    <div class="text-red-600 mb-4 text-5xl">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Hemat Energi</h3>
                    <p class="text-gray-600">Efisiensi tinggi hingga 70%</p>
                    <p class="text-sm text-gray-400 mt-2 italic">Klik untuk melihat detail</p>
                </div>

                <!-- Portabel -->
                <div @click="openModal = 'portabel'" class="cursor-pointer bg-gray-50 p-8 rounded-xl text-center shadow hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-red-600 mb-4 text-5xl">
                        <i class="fas fa-truck-moving"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Portabel</h3>
                    <p class="text-gray-600">Desain ringkas dan mudah dipindah</p>
                    <p class="text-sm text-gray-400 mt-2 italic">Klik untuk melihat detail</p>
                </div>

                <!-- Powerfull -->
                <div @click="openModal = 'powerfull'" class="cursor-pointer bg-gray-50 p-8 rounded-xl text-center shadow hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-red-600 mb-4 text-5xl">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Powerfull</h3>
                    <p class="text-gray-600">Kapasitas uap besar</p>
                    <p class="text-sm text-gray-400 mt-2 italic">Klik untuk melihat detail</p>
                </div>

                <!-- Ramah Lingkungan -->
                <div @click="openModal = 'eco'" class="cursor-pointer bg-gray-50 p-8 rounded-xl text-center shadow hover:shadow-lg transition" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-red-600 mb-4 text-5xl">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Ramah Lingkungan</h3>
                    <p class="text-gray-600">Menggunakan sistem downdraft gasification</p>
                    <p class="text-sm text-gray-400 mt-2 italic">Klik untuk melihat detail</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Produk Unggulan -->
    <section id="products" class="py-16 bg-gradient-to-b from-white to-red-100 relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Produk <span class="merah-gradient-text text-primary">Unggulan</span> Kami</h2>
                <div class="w-20 h-1 bg-primary mx-auto"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Product 1 -->
                <a href="#catalog" class="block product-card bg-white rounded-xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer" data-aos="fade-up">
                    <div class="relative h-64 bg-gray-100 overflow-hidden">
                        <img src="{{ asset('img/products/boiler.jpeg') }}" alt="Mesin Boiler" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                        <div class="absolute top-4 right-4 merah-gradient-background text-white text-sm font-bold px-3 py-1 rounded-full">
                            <i class="fas fa-fire mr-1"></i> Miniboiler
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Miniboiler</h3>
                        <p class="text-gray-600 mb-4">Sistem downdraft gassification dengan efisiensi tinggi</p>
                        <ul class="space-y-2 text-gray-700 mb-4">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Powerfull karena merupakan boiler 3 pass</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Penghematan bahan bakar hingga 85% lebih tinggi</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Ramah lingkungan</span>
                            </li>
                            
                        </ul>
                        <!-- Tombol Lihat Katalog dihapus karena sudah digantikan fungsi oleh anchor utama -->
                    </div>
                </a>
                
                <!-- Product 2 -->
                <a href="#catalog" class="block product-card bg-white rounded-xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative h-64 bg-gray-100 overflow-hidden">
                        <img src="{{ asset('img/products/penggorengan.jpg') }}" alt="Mesin Penggorengan" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                        <div class="absolute top-4 right-4 merah-gradient-background text-white text-sm font-bold px-3 py-1 rounded-full">
                            <i class="fas fa-temperature-high mr-1"></i> Penggorengan
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Mesin Penggorengan</h3>
                        <p class="text-gray-600 mb-4">Panel touchscreen dengan kontrol otomatis</p>
                        <ul class="space-y-2 text-gray-700 mb-4">
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Kontrol suhu otomatis</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Penghematan bahan bakar hingga 85% lebih tinggi</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Panas Tinggi & Stabil</span>
                            </li>
                        </ul>
                    </div>
                </a>

                <!-- Product 3 -->
                <a href="#catalog" class="block product-card bg-white rounded-xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative h-64 bg-gray-100 overflow-hidden">
                        <img src="{{ asset('img/products/autoclave.jpeg') }}" alt="Autoclave" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                        <div class="absolute top-4 right-4 merah-gradient-background text-white text-sm font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-laptop-code mr-1"></i> Autoclave
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Autoclave</h3>
                        <p class="text-gray-600 mb-4">Bisa digunakan untuk sterilisasi dan pelunakan pada industri makanan & kesehatan</p>
                        <ul class="space-y-2 text-gray-700 mb-4">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <span>Kontrol tekanan dan suhu otomatis</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <span>Desain tahan lama dan aman</span>
                        </li>
                        </ul>
                    </div>
                    </a>

                <!-- Product 4 -->
                <a href="#catalog" class="block product-card bg-white rounded-xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer" data-aos="fade-up">
                    <div class="relative h-64 bg-gray-100 overflow-hidden">
                        <img src="{{ asset('img/products/steambox.jpeg') }}" alt="Steambox" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                        <div class="absolute top-4 right-4 merah-gradient-background text-white text-sm font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-industry mr-1"></i> Steambox
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Steambox</h3>
                        <p class="text-gray-600 mb-4">Bisa digunakan untuk pengukusan</p>
                        <ul class="space-y-2 text-gray-700 mb-4">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <span>Distribusi uap merata & efisien</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <span>Kapasitas besar, ideal untuk skala UMKM</span>
                        </li>
                        </ul>
                    </div>
                    </a>

                <!-- Product 5 -->
                <a href="#catalog" class="block product-card bg-white rounded-xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative h-64 bg-gray-100 overflow-hidden">
                        <img src="{{ asset('img/products/bed_dryer.jpeg') }}" alt="Bed Dryer" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                        <div class="absolute top-4 right-4 merah-gradient-background text-white text-sm font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-tools mr-1"></i> Bed Dryer
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Bed Dryer</h3>
                        <p class="text-gray-600 mb-4">Sistem pengeringan efisien untuk kerupuk, beras, dan lainnya</p>
                        <ul class="space-y-2 text-gray-700 mb-4">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <span>Aliran udara merata & pengeringan lebih cepat</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <span>Konsumsi energi rendah</span>
                        </li>
                        </ul>
                    </div>
                </a>
                
                <!-- Product 6 -->
                <a href="#catalog" class="block product-card bg-white rounded-xl overflow-hidden shadow-lg transition-all duration-300 hover:shadow-xl hover:-translate-y-2 cursor-pointer" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative h-64 bg-gray-100 overflow-hidden">
                        <img src="{{ asset('img/products/lainnya.jpeg') }}" alt="Produk Lainnya" class="w-full h-full object-cover transition duration-500 hover:scale-105">
                        <div class="absolute top-4 right-4 merah-gradient-background text-white text-sm font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-leaf mr-1"></i> Eco
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Lainnya</h3>
                        <p class="text-gray-600 mb-4">Berbagai mesin tambahan yang mendukung efisiensi proses produksi Anda</p>
                        <ul class="space-y-2 text-gray-700 mb-4">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            <span>Teknologi terintegrasi dan ramah lingkungan</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Bisa disesuaikan dengan kebutuhan produksi</span>
                        </li>
                        </ul>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Katalog Produk Dinamis -->
    <section id="catalog" class="py-16 relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Katalog <span class="merah-gradient-text text-primary">Produk</span></h2>
                <div class="section-title"></div>
            </div>
            
            <div x-data="productCatalog()" x-init="fetchProducts()" class="mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <div class="relative w-full md:w-64">
                        <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchProducts()" placeholder="Cari produk..." 
                               class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition duration-300">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    
                    <div class="w-full md:w-auto">
                        <select x-model="categoryFilter" @change="fetchProducts()" 
                                class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary py-2 px-3 transition duration-300">
                            <option value="all">Semua Kategori</option>
                            <template x-for="category in categories" :key="category.id_kategori">
                                <option :value="category.id_kategori" x-text="category.nama_kategori"></option>
                            </template>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="product in products" :key="product.id_produk">
                        <div class="bg-white rounded-lg overflow-hidden shadow-md transition duration-300 hover:shadow-lg card-hover-effect" data-aos="fade-up">
                            <div class="h-60 bg-gray-200 flex items-center justify-center overflow-hidden relative">
                                <template x-if="product.images && product.images.length > 0">
                                    <img 
                                        x-bind:src="`${baseUrl}/storage/${product.images.find(img => img.is_primary)?.path || product.images[0].path}`" 
                                        x-bind:alt="product.nama_produk" 
                                        class="w-full h-full object-contain transition duration-500 hover:scale-105"
                                        loading="lazy"
                                        x-bind:onerror="`this.onerror=null;this.src='${baseUrl}/placeholder-product.jpg'`"
                                    >
                                </template>
                                <template x-if="!product.images || product.images.length === 0">
                                    <div class="text-gray-500 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-gray-800 mb-1" x-text="product.nama_produk"></h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2" x-text="product.spesifikasi || 'Tidak ada deskripsi'"></p>
                                <div class="flex justify-between items-center mt-4">
                                    <a :href="`${baseUrl}/produk_katalog/${product.id_produk}`" 
                                       class="text-sm merah-gradient-background hover:bg-secondary text-white py-2 px-4 rounded-full transition duration-300 inline-flex items-center">
                                        Detail Produk <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-8 flex justify-center" x-show="total > 0">
                    <template x-if="pagination.last_page > 1">
                        <div class="flex space-x-1">
                            <button @click="changePage(1)" :disabled="pagination.current_page === 1" 
                                    class="px-3 py-1 rounded border" 
                                    :class="{'bg-primary text-white': pagination.current_page === 1, 'hover:bg-gray-100': pagination.current_page !== 1}">
                                &laquo;
                            </button>
                            
                            <template x-for="page in pagination.last_page" :key="page">
                                <button @click="changePage(page)" 
                                        class="px-3 py-1 rounded border" 
                                        :class="{'bg-primary text-white': pagination.current_page === page, 'hover:bg-gray-100': pagination.current_page !== page}" 
                                        x-text="page"></button>
                            </template>
                            
                            <button @click="changePage(pagination.last_page)" :disabled="pagination.current_page === pagination.last_page" 
                                    class="px-3 py-1 rounded border" 
                                    :class="{'bg-primary text-white': pagination.current_page === pagination.last_page, 'hover:bg-gray-100': pagination.current_page !== pagination.last_page}">
                                &raquo;
                            </button>
                        </div>
                    </template>
                </div>
                
                <div class="text-center py-8" x-show="products.length === 0 && !loading">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-medium text-gray-600">Produk tidak ditemukan</h3>
                    <p class="text-gray-500">Coba gunakan kata kunci lain atau filter yang berbeda</p>
                </div>
                
                <div class="text-center py-8" x-show="loading">
                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-primary mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-2 text-gray-600">Memuat produk...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Documentation Section -->
    <section id="videos" class="py-8 md:py-16">
        <!-- Background ornaments -->
        <div class="pointer-events-none absolute -top-10 -left-10 w-48 h-48 bg-red-200/40 rounded-full blur-3xl animate-pulse"></div>
        <div class="pointer-events-none absolute bottom-0 right-0 w-72 h-72 bg-red-300/30 rounded-full blur-3xl animate-spin-slow"></div>
        <div class="container mx-auto px-4">
            <div class="text-center mb-8 md:mb-12" data-aos="fade-up">
                <h2 class="text-2xl md:text-4xl font-bold text-gray-800 mb-4">Dokumentasi <span class="merah-gradient-text text-red-600">Video</span></h2>
                <div class="w-20 h-1 bg-red-600 mx-auto"></div>
            </div>

            <div x-data="{
                videos: [
                    { id: 1, title: 'Produksi Briket', description: 'Produksi briket dengan teknologi terbaik yang menghasilkan produk berkualitas tinggi', videoUrl: 'https://www.youtube.com/embed/aqIIqWJuAmE', thumbnail: 'https://img.youtube.com/vi/aqIIqWJuAmE/maxresdefault.jpg' },
                    { id: 2, title: 'Coming Soon Antasena (Mesin Penggorengan', description: 'Mesin Penggorengan efisien, portabel, dan powerfull', videoUrl: 'https://www.youtube.com/embed/u-m7CIHPfJo', thumbnail: 'https://img.youtube.com/vi/u-m7CIHPfJo/maxresdefault.jpg' },
                    { id: 3, title: 'Mesin Penggorengan (Antasena)', description: 'Mesin penggorengan yang di uji coba di UKM Tahu', videoUrl: 'https://www.youtube.com/embed/R02VR969czI', thumbnail: 'https://img.youtube.com/vi/R02VR969czI/maxresdefault.jpg' },
                    { id: 4, title: 'Boiler Kumbakarna', description: 'Boiler raksasa dengan kapasitas 1,5 ton kg uap per jam', videoUrl: 'https://www.youtube.com/embed/-rJrX59oJqM', thumbnail: 'https://img.youtube.com/vi/-rJrX59oJqM/maxresdefault.jpg' },
                    { id: 5, title: 'Profil Pabrik Briket di Tasikmalaya', description: 'Profil pabrik briket di Tasikmalaya', videoUrl: 'https://www.youtube.com/embed/HH0kq4Hw4Qk', thumbnail: 'https://img.youtube.com/vi/HH0kq4Hw4Qk/maxresdefault.jpg' },
                ],
                activeVideo: null,
                currentIndex: 2,
                autoPlayTimeout: null,
                isPlaying: false,
                isTransitioning: false,
                touchStartX: 0,
                touchEndX: 0,

                nextVideo() {
                    if (this.isTransitioning) return;
                    this.isTransitioning = true;
                    this.currentIndex = (this.currentIndex + 1) % this.videos.length;
                    setTimeout(() => this.isTransitioning = false, 800);
                },

                prevVideo() {
                    if (this.isTransitioning) return;
                    this.isTransitioning = true;
                    this.currentIndex = (this.currentIndex - 1 + this.videos.length) % this.videos.length;
                    setTimeout(() => this.isTransitioning = false, 800);
                },

                openVideoModal(video) {
                    this.activeVideo = video;
                    this.isPlaying = false;
                },

                closeVideoModal() {
                    this.activeVideo = null;
                },

                copyVideoLink(videoUrl) {
                    navigator.clipboard.writeText(videoUrl).then(() => {
                        alert('Link video berhasil disalin!');
                    }).catch(err => {
                        console.error('Gagal menyalin link: ', err);
                    });
                },

                getVisibleVideos() {
                    if (window.innerWidth < 768) {
                        const result = [];
                        for (let i = -1; i <= 1; i++) {
                            const index = (this.currentIndex + i + this.videos.length) % this.videos.length;
                            result.push(this.videos[index]);
                        }
                        return result;
                    } else {
                        const result = [];
                        for (let i = -2; i <= 2; i++) {
                            const index = (this.currentIndex + i + this.videos.length) % this.videos.length;
                            result.push(this.videos[index]);
                        }
                        return result;
                    }
                },

                getVideoScale(index) {
                    if (window.innerWidth < 768) {
                        const scales = [0.9, 1.1, 0.9];
                        return scales[index];
                    } else {
                        const scales = [0.8, 0.9, 1.1, 0.9, 0.8];
                        return scales[index];
                    }
                },

                getVideoOpacity(index) {
                    if (window.innerWidth < 768) {
                        const opacities = [0.8, 1, 0.8];
                        return opacities[index];
                    } else {
                        const opacities = [0.7, 0.8, 1, 0.8, 0.7];
                        return opacities[index];
                    }
                },

                getVideoZIndex(index) {
                    if (window.innerWidth < 768) {
                        const zIndexes = [20, 30, 20];
                        return zIndexes[index];
                    } else {
                        const zIndexes = [10, 20, 30, 20, 10];
                        return zIndexes[index];
                    }
                },

                getVideoTransition(index) {
                    if (window.innerWidth < 768) {
                        const delays = ['75ms', '50ms', '75ms'];
                        return `all 600ms cubic-bezier(0.22, 0.61, 0.36, 1) ${delays[index]}`;
                    } else {
                        const delays = ['100ms', '75ms', '50ms', '75ms', '100ms'];
                        return `all 600ms cubic-bezier(0.22, 0.61, 0.36, 1) ${delays[index]}`;
                    }
                },

                getYoutubeEmbedUrl(url) {
                    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                    const match = url.match(regExp);
                    const videoId = (match && match[2].length === 11) ? match[2] : null;
                    return `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
                },

                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },

                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    let diffX = this.touchEndX - this.touchStartX;
                    if (Math.abs(diffX) > 50) {
                        if (diffX < 0) {
                            this.nextVideo();
                        } else {
                            this.prevVideo();
                        }
                    }
                }
            }" 
            @keydown.right="nextVideo" 
            @keydown.left="prevVideo">

                <!-- Video Carousel -->
                <div class="relative overflow-hidden py-4 md:py-8" 
                    @touchstart="handleTouchStart"
                    @touchend="handleTouchEnd">
                    <div class="flex items-center justify-center">
                        <div class="w-full max-w-6xl relative">
                            <!-- Navigation Arrows -->
                            <button @click="prevVideo" 
                                    class="absolute left-0 top-1/2 -translate-y-1/2 z-40 bg-black bg-opacity-50 text-white p-2 md:p-4 rounded-full -ml-2 md:-ml-4 hover:bg-red-600 transition-all duration-300 hover:scale-110">
                                <i class="fas fa-chevron-left text-sm md:text-xl"></i>
                            </button>
                            
                            <div class="flex items-center justify-center px-6 md:px-12">
                                <template x-for="(video, i) in getVisibleVideos()" :key="video.id">
                                    <div class="flex-shrink-0 w-1/3 md:w-1/5 px-1 md:px-2"
                                        :style="`
                                            transform: scale(${getVideoScale(i)});
                                            opacity: ${getVideoOpacity(i)};
                                            z-index: ${getVideoZIndex(i)};
                                            transition: ${getVideoTransition(i)};
                                        `">
                                        <div class="bg-white rounded-lg overflow-hidden shadow-lg h-full flex flex-col relative"
                                            :class="{'glow-red': (window.innerWidth < 768 ? i === 1 : i === 2)}">
                                            <!-- Video Thumbnail/Preview -->
                                            <div class="relative" :style="'padding-bottom: 177.78%'">
                                                <div class="absolute inset-0 bg-gray-200 flex items-center justify-center">
                                                    <img 
                                                        :src="video.thumbnail" 
                                                        :alt="video.title"
                                                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                                    />
                                                    <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center transition-opacity duration-300">
                                                        <i class="fas fa-play text-2xl md:text-4xl text-red-600"></i>
                                                    </div>
                                                    <button @click="openVideoModal(video)" 
                                                            class="absolute inset-0 w-full h-full opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                                        <span class="bg-red-600 text-white px-2 py-1 md:px-4 md:py-2 rounded-lg text-xs md:text-base">Putar Video</span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Video Info -->
                                            <div class="p-2 md:p-4 flex-grow transition-all duration-300">
                                                <h3 class="font-bold text-gray-800 mb-1 text-sm md:text-base" x-text="video.title"></h3>
                                                <p class="text-gray-600 text-xs md:text-sm mb-3" x-text="video.description"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <button @click="nextVideo" 
                                    class="absolute right-0 top-1/2 -translate-y-1/2 z-40 bg-black bg-opacity-50 text-white p-2 md:p-4 rounded-full -mr-2 md:-mr-4 hover:bg-red-600 transition-all duration-300 hover:scale-110">
                                <i class="fas fa-chevron-right text-sm md:text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Video Modal -->
                <div x-show="activeVideo" x-cloak 
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-2 md:p-4"
                    @click.self="closeVideoModal">
                    <div class="relative w-full max-w-4xl mx-2 md:mx-4">
                        <button @click="closeVideoModal" 
                                class="absolute -top-8 md:-top-12 right-0 text-white hover:text-red-400 text-xl md:text-3xl z-50">
                            <i class="fas fa-times"></i>
                        </button>
                        
                        <div class="bg-white rounded-xl overflow-hidden">
                            <div class="aspect-w-16 aspect-h-9 w-full">
                                <iframe 
                                    width="100%" 
                                    height="100%"
                                    :src="getYoutubeEmbedUrl(activeVideo?.videoUrl)"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    class="w-full min-h-[200px] md:min-h-[400px]"
                                ></iframe>
                            </div>
                            
                            <div class="p-4 md:p-6">
                                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2" x-text="activeVideo?.title"></h3>
                                <p class="text-gray-600 text-sm md:text-base mb-4" x-text="activeVideo?.description"></p>
                                <button @click="copyVideoLink(activeVideo?.videoUrl)" class="text-xs md:text-sm text-red-600 font-medium hover:text-red-800 transition">
                                    <i class="fas fa-link mr-1"></i> Salin Link Video
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<style>
    /* Enhanced Glow Effect */
    .glow-red {
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.8);
        animation: pulse 2s infinite alternate;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.8);
        }
        100% {
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.9);
        }
    }
    
    /* Smoother transitions */
    .transition-all {
        transition-property: all;
    }
    
    /* Prevent layout shifts during transition */
    .flex-shrink-0 {
        backface-visibility: hidden;
        transform-style: preserve-3d;
    }
    
    /* YouTube iframe responsive */
    .aspect-w-16 {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    }
    
    .aspect-h-9 iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

    <!-- Skema Pembelian -->
    <section class="py-16 relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Opsi <span class="merah-gradient-text text-primary">Pembelian</span></h2>
                <div class="section-title"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">Kami menawarkan berbagai skema pembelian untuk menyesuaikan kebutuhan bisnis Anda</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Skema Jual Putus -->
                <div class="skema-card bg-white p-6 rounded-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <div class="merah-gradient-background p-3 rounded-full mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Jual Putus</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Kepemilikan penuh mesin dengan pembayaran satu kali.</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Kepemilikan mesin sepenuhnya</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Garansi resmi dari pabrik</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Kebebasan penggunaan tanpa batas</span>
                        </li>
                    </ul>
                    <a href="https://wa.me/62812220033?text=Halo%20PT.%20Dahana%20Rekayasa%20Nusantara%2C%20saya%20tertarik%20dengan%20Skema%20Jual%20Putus%2C%20dengan%20pembayaran%20sekali%20dan%20kepemilikan%20penuh%20atas%20mesin.%20Saya%20ingin%20mendapatkan%20informasi%20lebih%20lanjut.%20Terima%20kasih."
                        target="_blank"
                        class="inline-block text-primary font-medium hover:text-secondary transition duration-300">
                        Konsultasi Sekarang <i class="fas fa-arrow-right ml-1"></i>
                    </a>

                </div>
                <!-- Skema SEBO -->
                <div class="skema-card bg-white p-6 rounded-lg" data-aos="fade-up">
                    <div class="flex items-center mb-4">
                        <div class="merah-gradient-background p-3 rounded-full mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">SEBO (Sewa Boiler)</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Kami memiliki program bernama SEBO atau sewa boiler yang lebih fleksibel untuk UMKM.</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Sewa peralatan tanpa modal besar</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Pembayaran bulanan yang terjangkau</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>maintenance & perbaikan dijamin perusahaan</span>
                        </li>
                    </ul>
                    <a href="https://wa.me/62812220033?text=Halo%20PT.%20Dahana%20Rekayasa%20Nusantara%2C%20saya%20tertarik%20dengan%20Skema%20SEBO%20(sewa%20boiler%20dengan%20pembayaran%20bulanan%20yang%20fleksibel)%2C%20dan%20ingin%20konsultasi%20lebih%20lanjut.%20Mohon%20informasinya."
                        target="_blank"
                        class="inline-block text-primary font-medium hover:text-secondary transition duration-300">
                        Konsultasi Sekarang <i class="fas fa-arrow-right ml-1"></i>
                    </a>

                </div>
                
                <!-- Skema Deposit Mesin -->
                <div class="skema-card bg-white p-6 rounded-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <div class="merah-gradient-background p-3 rounded-full mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">GreenTech (Deposit Mesin)</h3>
                    </div>
                    <p class="text-gray-600 mb-4">Gunakan mesin kami dengan komitmen pembelian briket.</p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Cocok untuk area pemukiman padat</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Biaya terjangkau</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Kualitas bahan bakar terjamin</span>
                        </li>
                    </ul>
                    <a href="https://wa.me/62812220033?text=Halo%20PT.%20Dahana%20Rekayasa%20Nusantara%2C%20saya%20tertarik%20dengan%20Skema%20Deposit%20Mesin%2C%20di%20mana%20saya%20bisa%20menggunakan%20mesin%20dengan%20komitmen%20pembelian%20briket.%20Saya%20ingin%20konsultasi%20lebih%20lanjut.%20Terima%20kasih."
                        target="_blank"
                        class="inline-block text-primary font-medium hover:text-secondary transition duration-300">
                        Konsultasi Sekarang <i class="fas fa-arrow-right ml-1"></i>
                    </a>

                </div>
                
                
            </div>
        </div>
    </section>

    <!-- Keunggulan Perusahaan -->
    <section id="features" class="py-16 bg-white relative overflow-hidden grid-pattern">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Mengapa Memilih <span class="merah-gradient-text text-primary">Kami</span></h2>
                <div class="section-title"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up">
                    <div class="feature-icon bg-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Kualitas Terjamin</h3>
                    <p class="text-gray-600">Produk kami melalui proses pengujian ketat untuk memastikan kualitas dan keandalan yang tinggi.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon bg-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3 flex items-center justify-between">Tim Profesional
                        <button type="button" id="openTeamModal" class="ml-4 inline-block px-4 py-2 merah-gradient-background text-white text-sm font-semibold rounded-md hover:opacity-90 transition">
                            Lihat Tim
                        </button>
                    </h3>
                    <p class="text-gray-600">Didukung oleh tim ahli berpengalaman di bidangnya masing-masing untuk memberikan solusi terbaik.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon bg-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Teknologi Terkini</h3>
                    <p class="text-gray-600">Menggunakan teknologi terbaru untuk memastikan produk kami selalu unggul dalam performa.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up">
                    <div class="feature-icon bg-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Garansi & Layanan</h3>
                    <p class="text-gray-600">Kami memberikan garansi produk dan layanan purna jual yang lengkap untuk kepuasan pelanggan.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon bg-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Fasilitas Modern</h3>
                    <p class="text-gray-600">Didukung oleh fasilitas produksi modern dengan teknologi terkini untuk menjamin kualitas produk.</p>
                </div>
                
                <div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon bg-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Ramah Lingkungan</h3>
                    <p class="text-gray-600">Proses produksi yang ramah lingkungan dengan efisiensi energi dan pengelolaan limbah yang bertanggung jawab.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Tim Profesional -->
    <div id="teamModal" class="fixed inset-0 z-50 hidden overflow-auto bg-black bg-opacity-50 flex items-center justify-center px-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full relative p-6">
            <button type="button" id="closeTeamModal" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900">
                <span aria-hidden="true" class="text-2xl">&times;</span>
                <span class="sr-only">Tutup</span>
            </button>
            <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Tim Profesional Kami</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Member 1 -->
                <div class="flex items-start gap-4 bg-gray-50 rounded-lg p-4">
                    <img src="{{ asset('img/nafi.jpeg') }}" alt="Nafi Rasyid Parikesit" class="w-20 h-20 rounded-full object-cover flex-shrink-0">
                    <div>
                        <h4 class="font-semibold text-lg">Nafi Rasyid Parikesit</h4>
                        <p class="text-sm text-gray-600">Chief Executive Officer</p>
                    </div>
                </div>
                <!-- Member 2 -->
                <div class="flex items-start gap-4 bg-gray-50 rounded-lg p-4">
                    <img src="{{ asset('img/egie.jpeg') }}" alt="Pak Egy Mantap" class="w-20 h-20 rounded-full object-cover flex-shrink-0">
                    <div>
                        <h4 class="font-semibold text-lg">Egie Helmi Fauzi</h4>
                        <p class="text-sm text-gray-600">Direktur Pengembangan Bisnis</p>
                    </div>
                </div>
                <!-- Member 3 -->
                <div class="flex items-start gap-4 bg-gray-50 rounded-lg p-4">
                    <img src="{{ asset('img/budi.jpeg') }}" alt="Budi Sulistiyo" class="w-20 h-20 rounded-full object-cover flex-shrink-0">
                    <div>
                        <h4 class="font-semibold text-lg">Budi Sulistiyo</h4>
                        <p class="text-sm text-gray-600">Direktur Keuangan</p>
                    </div>
                </div>
                <!-- Member 4 -->
                <div class="flex items-start gap-4 bg-gray-50 rounded-lg p-4">
                    <img src="{{ asset('img/dede.jpeg') }}" alt="Dede Miftahul Anwar" class="w-20 h-20 rounded-full object-cover flex-shrink-0">
                    <div>
                        <h4 class="font-semibold text-lg">Dede Miftahul Anwar</h4>
                        <p class="text-sm text-gray-600">Direktur IT</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-6">
                <button type="button" id="closeTeamModalFooter" class="px-6 py-2 bg-primary text-white font-medium rounded-md hover:opacity-90 transition">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Script toggling modal (vanilla JS) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openTeamModal');
            const modal = document.getElementById('teamModal');
            const closeBtns = [document.getElementById('closeTeamModal'), document.getElementById('closeTeamModalFooter')];

            function openModal() {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            openBtn?.addEventListener('click', openModal);
            closeBtns.forEach(btn => btn?.addEventListener('click', closeModal));

            // close when clicking outside content
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });

            // close on ESC
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>


    <!-- Perusahaan Kami -->
    <section id="companies" class="py-16 bg-white relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Jaringan <span class="merah-gradient-text text-primary">Perusahaan</span></h2>
                <div class="section-title"></div>
                <p class="text-gray-600 max-w-2xl mx-auto mt-4">Kami memiliki beberapa perusahaan yang berfokus pada bidang spesifik untuk memberikan solusi terbaik</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Ghava Shankara Nusantara -->
                <div class="bg-gray-50 p-8 rounded-xl text-center" data-aos="fade-up">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white rounded-full shadow-md flex items-center justify-center">
                        <img src="{{ asset('img/logo-ghava.png') }}" alt="Ghava Shankara Nusantara" class="h-12 object-contain company-logo">
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Ghava Shankara Nusantara</h3>
                    <p class="text-gray-600 mb-4">Perusahaan induk yang bergerak di bidang manufaktur mesin industri dan solusi teknis.</p>
                    <div class="flex justify-center space-x-2">
                        <span class="merah-gradient-background text-primary text-xs px-3 py-1 rounded-full">Manufaktur</span>
                        <span class="merah-gradient-background text-primary text-xs px-3 py-1 rounded-full">Teknologi</span>
                    </div>
                </div>
                
                <!-- Dahana Rekayasa Nusantara -->
                <div class="bg-gray-50 p-8 rounded-xl text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white rounded-full shadow-md flex items-center justify-center">
                        <img src="{{ asset('img/logo-dahana.png') }}" alt="Dahana Rekayasa Nusantara" class="h-12 object-contain company-logo">
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Dahana Rekayasa Nusantara</h3>
                    <p class="text-gray-600 mb-4">Bergerak di bidang pemasaran boiler dan solusi energi untuk UMKM dan industri menengah.</p>
                    <div class="flex justify-center space-x-2">
                        <span class="merah-gradient-background text-primary text-xs px-3 py-1 rounded-full">Pemasaran</span>
                        <span class="merah-gradient-background text-primary text-xs px-3 py-1 rounded-full">Energi</span>
                    </div>
                </div>
                
                <!-- Syirkah Utama Nusantara -->
                <div class="bg-gray-50 p-8 rounded-xl text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white rounded-full shadow-md flex items-center justify-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Syirkah Utama Nusantara" class="h-12 object-contain company-logo">
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Syirkah Utama Nusantara</h3>
                    <p class="text-gray-600 mb-4">Bergerak di bidang pengelolaan investasi mesin dengan prinsip kemitraan yang saling menguntungkan.</p>
                    <div class="flex justify-center space-x-2">
                        <span class="merah-gradient-background text-primary text-xs px-3 py-1 rounded-full">Investasi</span>
                        <span class="merah-gradient-background text-primary text-xs px-3 py-1 rounded-full">Kemitraan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- UKM Users Section -->
    <section class="py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Digunakan Oleh <span class="merah-gradient-text text-red-600">UKM</span>
        </h2>
        <div class="w-20 h-1 bg-blue-600 mx-auto"></div>
        <p class="text-gray-600 max-w-2xl mx-auto mt-4">
            Berbagai jenis usaha kecil dan menengah telah merasakan manfaat teknologi miniboiler kami
        </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- UKM Item -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/tahu.jpeg') }}"
                alt="Pengrajin Tahu & Tempe"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-egg text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">
                Pengrajin Tahu & Tempe
            </h3>
            <p class="text-gray-600 text-sm">
                Proses perebusan, pencetakan, dan pengolahan
            </p>
            </div>
        </div>

        <!-- UKM 2 -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
            data-aos-delay="100"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/baso.jpeg') }}"
                alt="Baso & Makanan Olahan"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-drumstick-bite text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">
                Baso & Makanan Olahan
            </h3>
            <p class="text-gray-600 text-sm">Proses perebusan dan pencetakan</p>
            </div>
        </div>

        <!-- UKM 3 -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
            data-aos-delay="200"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/minuman.png') }}"
                alt="Minuman Kemasan"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-wine-bottle text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Minuman Kemasan</h3>
            <p class="text-gray-600 text-sm">
                Agar-agar, jelly, cingcau, nata de coco
            </p>
            </div>
        </div>

        <!-- UKM 4 -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
            data-aos-delay="300"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/atsiri.jpg') }}"
                alt="Penyulingan Minyak Atsiri"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-oil-can text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">
                Penyulingan Minyak Atsiri
            </h3>
            <p class="text-gray-600 text-sm">
                Nilam, akar wangi, sereh wangi, kayu putih
            </p>
            </div>
        </div>

        <!-- UKM 5 -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/kerupuk.jpeg') }}"
                alt="Pabrik Kerupuk"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-cookie text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Pabrik Kerupuk</h3>
            <p class="text-gray-600 text-sm">
                Proses adonan tajin, pengukusan, pengeringan
            </p>
            </div>
        </div>

        <!-- UKM 6 -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
            data-aos-delay="100"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/rpa.png') }}"
                alt="Oven Pengering"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-wind text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">RPA</h3>
            <p class="text-gray-600 text-sm">
                Perebusan, dan proses lainya yang menggunakan uap panas
            </p>
            </div>
        </div>

        <!-- UKM 7 -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
            data-aos-delay="200"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/laundry.png') }}"
                alt="Laundry/Garment"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-tshirt text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Laundry/Garment</h3>
            <p class="text-gray-600 text-sm">
                Setrika uap, pengering, washing
            </p>
            </div>
        </div>

        <!-- UKM 8 -->
        <div
            class="bg-gray-50 rounded-lg overflow-hidden shadow hover:shadow-md transition"
            data-aos="fade-up"
            data-aos-delay="300"
        >
            <div class="relative h-70 overflow-hidden">
            <img
                src="{{ asset('img/ukm/jamur.png') }}"
                alt="Budidaya Jamur"
                class="w-full h-full object-cover"
            />
            <div
                class="absolute top-2 right-2 bg-blue-100 rounded-full p-2 flex items-center justify-center"
            >
                <i class="fas fa-fan text-red-600 text-xl"></i>
            </div>
            </div>
            <div class="p-4 text-center">
            <h3 class="text-lg font-bold text-gray-800 mb-1">Budidaya Jamur</h3>
            <p class="text-gray-600 text-sm">
                Proses sterilisasi bag log & kubung
            </p>
            </div>
        </div>
        </div>
    </div>
    </section>

    <!-- Testimoni Klien -->
    <!-- <section class="py-16 bg-gray-50 relative overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Apa Kata <span class="merah-gradient-text text-primary">Klien</span> Kami</h2>
                <div class="section-title"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="testimonial-card bg-white p-6 rounded-lg" data-aos="fade-up">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span class="text-primary font-bold text-lg">AS</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Ahmad Syafii</h4>
                            <p class="text-sm text-gray-600">Direktur, PT Maju Jaya</p>
                        </div>
                    </div>
                    <div class="text-gray-600 mb-4">
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <p class="text-gray-700 italic">"Mesin boiler dari perusahaan ini benar-benar mengubah efisiensi produksi kami. Penghematan energi mencapai 25% dalam 3 bulan pertama penggunaan."</p>
                </div>
                
                <div class="testimonial-card bg-white p-6 rounded-lg" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span class="text-primary font-bold text-lg">LS</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Lisa Sari</h4>
                            <p class="text-sm text-gray-600">Manajer Operasional, PT Sejahtera Abadi</p>
                        </div>
                    </div>
                    <div class="text-gray-600 mb-4">
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <p class="text-gray-700 italic">"Aplikasi ERP mereka sangat membantu dalam mengintegrasikan seluruh operasional perusahaan. Laporan real-time memudahkan pengambilan keputusan."</p>
                </div>
                
                <div class="testimonial-card bg-white p-6 rounded-lg" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                            <span class="text-primary font-bold text-lg">RB</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Rudi Budiman</h4>
                            <p class="text-sm text-gray-600">Pemilik, UD Makmur Jaya</p>
                        </div>
                    </div>
                    <div class="text-gray-600 mb-4">
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-yellow-400 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <svg class="w-5 h-5 text-gray-300 inline-block mb-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </div>
                    <p class="text-gray-700 italic">"Program kemitraan memberikan keuntungan yang stabil dengan prinsip yang adil. Sangat cocok untuk investasi jangka panjang."</p>
                </div>
            </div>
        </div>
    </section> -->

    <!-- CTA Section -->
    <section class="py-16 merah-gradient-background text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6" data-aos="fade-up">Siap Meningkatkan Efisiensi Produksi Anda?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">Hubungi kami sekarang untuk konsultasi gratis dan solusi terbaik untuk kebutuhan bisnis Anda.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="200">
                <a href="https://wa.me/62812220033?text=Selamat%20Pagi%20PT.%20Dahana%20Rekayasa%20Nusantara%2C%20saya%20memperoleh%20informasi%20mengenai%20perusahaan%20ini%20dari%20website%2C%20saya%20ingin%20menanyakan%20lebih%20lanjut%20terkait%20produk%20di%20perusahaan%20ini." 
                   target="_blank" 
                   class="bg-white text-red-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-full text-lg transition duration-300 inline-flex items-center justify-center">
                    <i class="fab fa-whatsapp mr-2 text-xl"></i> WhatsApp Kami
                </a>
                <!-- <a href="tel:+68112220033" 
                   class="bg-transparent border-2 border-white hover:bg-white hover:text-red-600 font-bold py-3 px-8 rounded-full text-lg transition duration-300 inline-flex items-center justify-center">
                    <i class="fas fa-phone-alt mr-2"></i> Telepon Kami
                </a> -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Company Info -->
                <div>
                    <img src="{{ asset('img/logo-auto.png') }}" alt="Ghava Shankara Nusantara" class="h-10 mb-4">
                    <p class="text-gray-400 mb-4">Perusahaan manufaktur teknologi tepat guna "Miniboiler" dan "biomass" bahan bakar ramah lingkungan, yang fokus memenuhi kebutuhan teknologi UMKM.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Tautan Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition">Beranda</a></li>
                        <li><a href="#products" class="text-gray-400 hover:text-white transition">Produk</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-white transition">Keunggulan</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#companies" class="text-gray-400 hover:text-white transition">Perusahaan</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Kontak Kami</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-red-600"></i>
                            <span>Lingkungan Industri Kecil (LIK) No. B2-B5 BPI Logam, Jl. Soekarno Hatta, KM 12,5 Gedebage Bandung 40296</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope mt-1 mr-3 text-red-600"></i>
                            <span>egie@dahana-boiler.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fab fa-whatsapp mt-1 mr-3 text-red-600"></i>
                            <span>081-222-0033</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Newsletter -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Newsletter</h3>
                    <p class="text-gray-400 mb-4">Dapatkan informasi terbaru tentang produk dan penawaran spesial dari kami.</p>
                    <form class="flex">
                        <input type="email" placeholder="Alamat Email" class="px-4 py-2 w-full rounded-l-lg focus:outline-none text-gray-900">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 rounded-r-lg transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">© 2025 Dahana Rekayasa Nusantara. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <a href="#home" id="back-to-top" class="fixed bottom-8 right-8 bg-red-600 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg hover:bg-red-700 transition duration-300 opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Modal Template -->
    <div x-show="openModal" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl p-6 max-w-xl w-full mx-4 relative overflow-y-auto max-h-[90vh]" @click.outside="openModal = null">
                <button class="absolute top-2 right-3 text-gray-500 hover:text-red-600 text-2xl" @click="openModal = null">&times;</button>

                <!-- Modal: Hemat Energi -->
                <div x-show="openModal === 'hemat'" x-transition>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Contoh Penghematan</h3>
                <div class="bg-gray-100 rounded-lg p-4 shadow-inner">
                    <p class="text-gray-700 mb-2 font-medium">📍 Studi Kasus: Pabrik Kerupuk – Ciamis</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2 text-gray-700">
                        <li>Menggunakan sistem 3 phase yang lebih efisien dibandingkan sistem konvensional</li>
                        <li>Penggunaan bahan bakar optimal dengan pembakaran sempurna</li>
                        <li>Penghematan biaya operasional hingga 85% dibandingkan boiler biasa</li>
                        <li>Insulasi termal terbaik mengurangi kehilangan panas</li>
                    </ul>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Before -->
                    <div class="bg-red-50 border border-red-200 p-4 rounded-xl">
                        <h4 class="font-semibold text-red-600 mb-2">Sebelum Menggunakan Boiler Kami</h4>
                        <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                        <li>Menghabiskan 1 truk kayu bakar per hari</li>
                        <li>Biaya operasional: ± Rp1.200.000 per hari</li>
                        <li>Efisiensi pembakaran rendah</li>
                        </ul>
                    </div>

                    <!-- After -->
                    <div class="bg-green-50 border border-green-200 p-4 rounded-xl">
                        <h4 class="font-semibold text-green-700 mb-2">Setelah Menggunakan Boiler Kami</h4>
                        <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                        <li>1 truk kayu habis dalam 4–5 hari</li>
                        <li>Biaya operasional turun drastis</li>
                        <li>Efisiensi pembakaran lebih dari 3x lipat</li>
                        </ul>
                    </div>
                    </div>
                </div>
                </div>


                <!-- Modal: Portabel -->
                <div x-show="openModal === 'portabel'" x-transition>
                    <h3 class="text-2xl font-bold text-green-600 mb-2">Portabel</h3>
                    <p class="text-gray-700">Keunggulan boiler kami memberikan fleksibilitas operasional:</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2 text-gray-700">
                        <li>Desain kompak dengan dimensi yang lebih kecil dari boiler konvensional</li>
                        <li>Dilengkapi roda untuk memudahkan perpindahan lokasi</li>
                        <li>Berat yang lebih ringan namun tetap kuat dan tahan lama</li>
                        <li>Instalasi cepat dan mudah tanpa konstruksi khusus</li>
                        <li>Cocok untuk operasional di berbagai lokasi</li>
                    </ul>
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <h4 class="font-bold text-red-800 mb-2">Keuntungan:</h4>
                        <p>Memungkinkan UKM untuk memindahkan peralatan sesuai kebutuhan produksi.</p>
                    </div>
                </div>

                <!-- Modal: Powerfull -->
                <div x-show="openModal === 'powerfull'" x-transition>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Bukti Kinerja</h3>
                <div class="bg-gray-100 rounded-lg p-4 shadow-inner">
                    <p class="text-gray-700 mb-2 font-medium">📍 Studi Kasus: Pabrik Tahu – Tasikmalaya</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <!-- Before -->
                    <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-xl">
                        <h4 class="font-semibold text-yellow-700 mb-2">Sebelum Menggunakan Boiler Kami</h4>
                        <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                        <li>Proses masak tahu memakan waktu ± 45 menit</li>
                        <li>Menggunakan sumber energi gas elpiji</li>
                        <li>Kapasitas produksi terbatas</li>
                        </ul>
                    </div>

                    <!-- After -->
                    <div class="bg-green-50 border border-green-200 p-4 rounded-xl">
                        <h4 class="font-semibold text-green-700 mb-2">Setelah Menggunakan Boiler Kami</h4>
                        <ul class="text-sm text-gray-700 space-y-2 list-disc list-inside">
                        <li>Proses masak hanya ± 15 menit</li>
                        <li>Produksi lebih cepat & efisien</li>
                        <li>Kapasitas produksi harian meningkat signifikan</li>
                        </ul>
                    </div>
                    </div>
                </div>
                </div>


                <!-- Modal: Ramah Lingkungan -->
                <div x-show="openModal === 'eco'" x-transition>
                    <h3 class="text-2xl font-bold text-teal-600 mb-2">Ramah Lingkungan</h3>
                    <p class="text-gray-700">Kami berkomitmen pada teknologi yang berkelanjutan:</p>
                    <ul class="list-disc pl-6 mt-4 space-y-2 text-gray-700">
                        <li>Menggunakan downdraft gassification yang menghasilkan pembakaran sempurna</li>
                        <li>Emisi karbon lebih rendah dibandingkan boiler konvensional</li>
                        <li>Mendukung penggunaan bahan bakar biomassa terbarukan</li>
                        <li>Sistem filtrasi asap mengurangi polusi udara</li>
                        <li>Efisiensi bahan bakar mengurangi limbah produksi</li>
                    </ul>
                </div>
            </div>
        </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS animation
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // GSAP animations
        gsap.registerPlugin(ScrollTrigger);
        
        // Animate hero elements
        gsap.from(".hero-section h1", {
            duration: 1,
            y: 50,
            opacity: 0,
            ease: "power3.out"
        });
        
        gsap.from(".hero-section p", {
            duration: 1,
            y: 50,
            opacity: 0,
            delay: 0.2,
            ease: "power3.out"
        });
        
        gsap.from(".hero-section .btn-primary", {
            duration: 1,
            y: 50,
            opacity: 0,
            delay: 0.4,
            ease: "power3.out"
        });
        
        // Animate keyword items
        document.querySelectorAll('.keyword-item').forEach((item, index) => {
            gsap.to(item, {
                scrollTrigger: {
                    trigger: item,
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                opacity: 1,
                x: 0,
                duration: 0.5,
                delay: index * 0.2,
                ease: "power2.out"
            });
        });
        
        // Animate product cards on scroll
        gsap.utils.toArray(".product-card").forEach((card, i) => {
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 80%",
                    toggleActions: "play none none none"
                },
                y: 50,
                opacity: 0,
                duration: 0.8,
                delay: i * 0.1,
                ease: "back.out(1.7)"
            });
        });

        // Product catalog Alpine.js component
        window.baseUrl = @json(url('/'));
        
        document.addEventListener('alpine:init', () => {
            Alpine.data('productCatalog', () => ({
                products: [],
                categories: [],
                searchQuery: '',
                categoryFilter: 'all',
                loading: false,
                pagination: {
                    current_page: 1,
                    last_page: 1
                },
                total: 0,
                
                fetchProducts() {
                    this.loading = true;
                    fetch(`${baseUrl}/api/products?page=${this.pagination.current_page}&search=${this.searchQuery}&category=${this.categoryFilter}`, {
                        headers: {
                            'Cache-Control': 'max-age=3600' // 1 jam chache nya
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.products = data.data;
                            this.pagination = {
                                current_page: data.pagination.current_page,
                                last_page: data.pagination.last_page
                            };
                            this.total = data.pagination.total;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error fetching products:', error);
                            this.loading = false;
                        });
                },
                
                fetchCategories() {
                    fetch(`${baseUrl}/api/categories`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.categories = data;
                        })
                        .catch(error => {
                            console.error('Error fetching categories:', error);
                        });
                },
                
                changePage(page) {
                    this.pagination.current_page = page;
                    this.fetchProducts();
                },
                
                formatNumber(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                },
                
                init() {
                    this.fetchProducts();
                    this.fetchCategories();
                }
            }));
        });
    </script>
</body>
</html>
